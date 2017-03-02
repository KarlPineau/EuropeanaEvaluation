<?php

namespace EntityBundle\Service;

use APIBundle\Service\log;
use Doctrine\ORM\EntityManager;
use EntityBundle\Entity\EntityRelation;
use MessengerBundle\Service\stringify;

class similarItems
{
    protected $em;
    protected $buzz;
    protected $log;
    protected $process;
    protected $graph;
    protected $stringify;

    public function __construct(EntityManager $EntityManager, $buzz, log $log, process $process, graph $graph, stringify $stringify)
    {
        $this->em = $EntityManager;
        $this->buzz = $buzz;
        $this->log = $log;
        $this->process = $process;
        $this->graph = $graph;
        $this->stringify = $stringify;
    }

    public function computeSimilarity($record, $deepLevel)
    {
        set_time_limit(0);

        $record = (object) $record;

        //if($this->graph->getLevel($record->europeana_id, null) <= 4) {
            /*$relations = $this->graph->getRelations($record->europeana_id);
            $algorithmsGenerated = array();
            foreach ($relations as $relation) {
                $algorithmsGenerated = $relation['algorithm'];
            }*/

            foreach ($this->getAlgoSI() as $algorithm) {
                //if (!array_search($algorithmsGenerated, $algorithmsGenerated)) {
                    $this->runProcess($record, $algorithm, $deepLevel);
                //}
            }
        //}
    }

    public function getAlgoSI()
    {
        $algorithms = array();

        $algorithms[] = 'defaultAlgorithm';
        $algorithms[] = 'randomItemAlgorithm';
        $algorithms[] = 'agnosticAlgorithm';
        $algorithms[] = 'chronologicalAlgorithm';
        $algorithms[] = 'typologicalAlgorithm';
        $algorithms[] = 'europeanaPublishingFrameworkAlgorithm';

        return $algorithms;
    }

    public function runProcess($record, $algorithm, $deepLevel)
    {
        $record = (object) $record;
        $sub = $this->runAlgorithm($record, $algorithm);

        $this->log->log("------------------------------------------------------------------", 'entity');
        $this->log->log("-> entity.similarItems->runProcess() -- ".date('Y/m/d h:i:s a', time()), 'entity');

        if($sub != null) {
            $this->log->log("-> Europeana_id: ".$sub, 'entity');

            if($this->em->getRepository('EntityBundle:EntityRelation')->findOneBy(array('entity1' => $record->europeana_id, 'entity2' => $sub)) == null) {
                if ($this->process->checkIsset($sub) == false) {
                    $this->log->log("-> Isset item ? NO", 'entity');
                    $subRecord = (object)$this->process->registerRecord($this->process->buildRecord($this->process->getRecord($sub)));

                    $relation = new EntityRelation();
                    $relation->setEntity1($record->europeana_id);
                    $relation->setEntity2($sub);
                    $relation->setAlgorithm($algorithm);
                    $this->em->persist($relation);
                    $this->em->flush();

                    if ($deepLevel > 1) {
                        $this->computeSimilarity($subRecord, $deepLevel - 1);
                    }
                } else {
                    $this->log->log("-> Isset item ? YES", 'entity');
                    $relation = new EntityRelation();
                    $relation->setEntity1($record->europeana_id);
                    $relation->setEntity2($sub);
                    $relation->setAlgorithm($algorithm);
                    $this->em->persist($relation);
                    $this->em->flush();
                }
            } else {
                $this->log->log("!-> Relation already existing", 'entity');
            }
        } else {
            $this->log->log("!-> Europeana_id: NULL", 'entity');
        }
    }

    public function runAlgorithm($object, $algorithm)
    {
        $this->buzz->getClient()->setTimeout(0);
        //Must return europeana_id
        $query = $this->{$algorithm}($object);

        $this->log->log("------------------------------------------------------------------", 'entity');
        $this->log->log("-> entity.similarItems->runAlgorithm() -- ".date('Y/m/d h:i:s a', time()), 'entity');

        $this->log->log(date('Y/m/d h:i:s a', time())." -- New Query Algo", 'entity');
        $this->log->log("-> Algo: ".$algorithm, 'entity');
        $this->log->log("-> Query: ".urldecode($query), 'entity');
        $this->log->log("-> QueryRefine: ".str_replace(" ", "+" , urldecode($query)), 'entity');

        $queryResponse = $this->buzz->get($query);
        if($queryResponse !== FALSE) {
            $this->log->log(date('Y/m/d h:i:s a', time())." -- Succeed query", 'entity');
            $this->log->log($queryResponse, 'entity');

            $content = (object) json_decode($queryResponse->getContent());
            $this->log->log(json_encode($content), 'entity');

            if(property_exists($content, 'items')) {
                foreach($content->items as $key => $item) {
                    if($key == 0) {
                        $this->log->log("-> Item: ".$content->items[0]->id, 'entity');
                        return $content->items[0]->id;
                    }
                }
            } else {
                $this->log->log("!-> No Item", 'entity');
                return null;
            }
        } else {
            $this->log->log(date('Y/m/d h:i:s a', time())." -- Failed query", 'entity');
            return null;
        }


    }

    public function defaultAlgorithm($object)
    {
        $this->log->log("------------------------------------------------------------------", 'entity');
        $this->log->log("-> entity.similarItems->defaultAlgorithm() -- ".date('Y/m/d h:i:s a', time()), 'entity');

        $object = (object) $object;
        $q = '';

        if($object->dcType != null) {
            $q .= "what:(".$this->stringify->stringify($object->dcType, ' OR ', true).")";
        }
        if($object->dcSubject != null) {
            $q .= "what:(".$this->stringify->stringify($object->dcSubject, ' OR ', true).")";
        }
        if($object->dcCreator != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "who:(".$this->stringify->stringify($object->dcCreator, ' OR ', true).")";
        }
        if($object->dcTitle != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "title:(".$this->stringify->stringify($object->dcTitle, ' OR ', true).")";
        }
        if($object->edmDataProvider != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "DATA_PROVIDER:\"".$this->stringify->stringify($object->edmDataProvider, ' OR ', true)."\"";
        }
        if($object->europeana_id != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "NOT europeana_id:\"".$this->stringify->stringify($object->europeana_id, ' OR ', true)."\"";
        }

        $data = array(
            'query' => $q,
            'rows' => 1,
            'wskey' => "api2demo");


        $this->log->log(date('Y/m/d h:i:s a', time())." -- Generating query", 'entity');
        $this->log->log(urldecode('https://www.europeana.eu/api/v2/search.json?'.http_build_query($data)), 'entity');

        return 'https://www.europeana.eu/api/v2/search.json?'.http_build_query($data);
    }

    public function europeanaPublishingFrameworkAlgorithm($object)
    {
        $this->log->log("------------------------------------------------------------------", 'entity');
        $this->log->log("-> entity.similarItems->europeanaPublishingFrameworkAlgorithm() -- ".date('Y/m/d h:i:s a', time()), 'entity');

        $object = (object) $object;
        $q = '';
        $spec = '';

        switch ($this->stringify->stringify($object->edmType, '', false)) {
            case 'IMAGE':
                $spec = "qf=IMAGE_SIZE%3Amedium&qf=IMAGE_SIZE%3Alarge&qf=IMAGE_SIZE%3Aextra_large&thumbnail=true";
                break;
            case 'TEXT':
                $spec = "qf=TEXT_FULLTEXT%3Atrue";
                break;
            case 'SOUND':
                $spec = "qf=SOUND_DURATION%3Avery_short&qf=SOUND_DURATION%3Ashort&qf=SOUND_DURATION%3Amedium&qf=SOUND_DURATION%3Along";
                break;
            case 'VIDEO':
                $spec = "qf=VIDEO_DURATION%3Ashort&qf=VIDEO_DURATION%3Amedium&qf=VIDEO_DURATION%3Along";
                break;
            case '3D':
                $spec = "qf=TYPE:3D";
                break;
        }

        if($object->dcType != null) {
            $q .= "what:(".$this->stringify->stringify($object->dcType, ' OR ', true).")";
        }
        if($object->dcSubject != null) {
            $q .= "what:(".$this->stringify->stringify($object->dcSubject, ' OR ', true).")";
        }
        if($object->dcCreator != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "who:(".$this->stringify->stringify($object->dcCreator, ' OR ', true).")";
        }
        if($object->dcTitle != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "title:(".$this->stringify->stringify($object->dcTitle, ' OR ', true).")";
        }
        if($object->edmDataProvider != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "DATA_PROVIDER:\"".$this->stringify->stringify($object->edmDataProvider, ' OR ', true)."\"";
        }
        if($object->europeana_id != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "NOT europeana_id:\"".$this->stringify->stringify($object->europeana_id, ' OR ', true)."\"";
        }

        $data = array(
            'query' => $q,
            'rows' => 1,
            'wskey' => "api2demo");


        $this->log->log(date('Y/m/d h:i:s a', time())." -- Generating query", 'entity');
        $this->log->log(urldecode('https://www.europeana.eu/api/v2/search.json?'.$spec.'&'.http_build_query($data)), 'entity');

        return 'https://www.europeana.eu/api/v2/search.json?'.$spec.'&'.http_build_query($data);
    }

    public function randomItemAlgorithm($object)
    {
        $this->log->log("------------------------------------------------------------------", 'entity');
        $this->log->log("-> entity.similarItems->randomItemAlgorithm() -- ".date('Y/m/d h:i:s a', time()), 'entity');

        $countries = array("Austria", "Belgium", "Bulgaria", "Czech Republic", "Denmark", "Estonia", "Finland", "France", "Germany", "Greece", "Hungary", "Iceland", "Ireland", "Israel", "Italy", "Latvia", "Lithuania", "Luxembourg", "Malta", "Netherlands", "Norway", "Poland", "Portugal", "Romania", "Russia", "Slovakia", "Slovenia", "Spain", "Sweden", "Switzerland", "Ukraine", "United Kingdom");
        $country = $countries[rand(0, (count($countries)-1))];
        $types = ['IMAGE', 'TEXT', 'ART', 'MUSIC'];
        $type = $types[rand(0, (count($types)-1))];
        $sorts = ['timestamp_update', 'timestamp_created', 'europeana_id'];
        $sort = $sorts[rand(0, (count($sorts)-1))];
        $sortsOrder = ['DESC', 'ASC'];
        $sortOrder = $sortsOrder[rand(0, (count($sortsOrder)-1))];

        $art = 'qf=(DATA_PROVIDER:"Östasiatiska museet" NOT TYPE:TEXT) OR (DATA_PROVIDER:"Medelhavsmuseet") OR (DATA_PROVIDER:"Rijksmuseum") OR (europeana_collectionName: "91631_Ag_SE_SwedishNationalHeritage_shm_art") OR (DATA_PROVIDER:"Bibliothèque municipale de Lyon") OR (DATA_PROVIDER:"Museu Nacional d\'Art de Catalunya") OR (DATA_PROVIDER:"Victoria and Albert Museum") OR (DATA_PROVIDER:"Slovak national gallery") OR (DATA_PROVIDER:"Thyssen-Bornemisza Museum") OR (DATA_PROVIDER:"Museo Nacional del Prado") OR (DATA_PROVIDER:"Statens Museum for Kunst") OR (DATA_PROVIDER:"Hungarian University of Fine Arts, Budapest") OR (DATA_PROVIDER:"Hungarian National Museum") OR (DATA_PROVIDER:"Museum of Applied Arts, Budapest") OR (DATA_PROVIDER:"Szépművészeti Múzeum") OR (DATA_PROVIDER:"Museum of Fine Arts - Hungarian National Gallery, Budapest") OR (DATA_PROVIDER:"Schola Graphidis Art Collection. Hungarian University of Fine Arts - High School of Visual Arts, Budapest") OR (PROVIDER:"Ville de Bourg-en-Bresse") OR (DATA_PROVIDER:"Universitätsbibliothek Heidelberg") OR ((what:("fine art" OR "beaux arts" OR "bellas artes" OR "belle arti" OR "schone kunsten" OR konst OR "bildende kunst" OR "Opere d\'arte visiva" OR "decorative arts" OR konsthantverk OR "arts décoratifs" OR paintings OR schilderij OR pintura OR peinture OR dipinto OR malerei OR måleri OR målning OR sculpture OR skulptur OR sculptuur OR beeldhouwwerk OR drawing OR poster OR tapestry OR gobelin OR jewellery OR miniature OR prints OR träsnitt OR holzschnitt OR woodcut OR lithography OR chiaroscuro OR "old master print" OR estampe OR porcelain OR mannerism OR rococo OR impressionism OR expressionism OR romanticism OR "Neo-Classicism" OR "Pre-Raphaelite" OR Symbolism OR Surrealism OR Cubism OR "Art Deco" OR "Art Déco" OR Dadaism OR "De Stijl" OR "Pop Art" OR "art nouveau" OR "art history" OR "http://vocab.getty.edu/aat/300041273" OR "histoire de l\'art" OR kunstgeschichte OR "estudio de la historia del arte" OR Kunstgeschiedenis OR "illuminated manuscript" OR buchmalerei OR enluminure OR "manuscrito illustrado" OR "manoscritto miniato" OR boekverluchting OR kalligrafi OR calligraphy OR exlibris)) AND (provider_aggregation_edm_isShownBy:*)) NOT (what: "printed serial" OR what:"printedbook" OR "printing paper" OR "printed music" OR DATA_PROVIDER:"NALIS Foundation" OR DATA_PROVIDER:"Ministère de la culture et de la communication, Musées de France" OR DATA_PROVIDER:"CER.ES: Red Digital de Colecciones de museos de España" OR PROVIDER:"OpenUp!" OR PROVIDER:"BHL Europe" OR PROVIDER:"EFG - The European Film Gateway" OR DATA_PROVIDER: "Malta Aviation Museum Foundation" OR DATA_PROVIDER:"National Széchényi Library - Digital Archive of Pictures" OR PROVIDER:"Swiss National Library")';
        $music = 'qf=(PROVIDER:"Europeana Sounds" AND provider_aggregation_edm_isShownBy:* AND music) OR (DATA_PROVIDER: "National Library of France" AND musique) OR (DATA_PROVIDER:"Sächsische Landesbibliothek - Staats- und Universitätsbibliothek Dresden" AND TYPE:SOUND) OR (edm_datasetName:" 09301_Ag_EU_Judaica_mcy78") OR (DATA_PROVIDER:"Kirsten Flagstadmuseet") OR (DATA_PROVIDER:"Ringve Musikkmuseum") OR (DATA_PROVIDER:"Netherlands Institute for Sound and Vision" AND provider_aggregation_edm_isShownBy:* AND (music OR muziek)) OR  (DATA_PROVIDER:"TV3 Televisió de Catalunya (TVC)" AND provider_aggregation_edm_isShownBy:* AND musica) OR (PROVIDER:"Institut National de l\'Audiovisuel" AND (musique OR opera OR pop OR rock OR concert OR chanson OR interpretation)) OR ((what:(music OR musique OR musik OR musica OR musicales OR "zenés előadás" OR "notated music" OR "folk songs" OR jazz OR "sheet music" OR score OR "musical instrument" OR partitur OR partituras OR gradual OR libretto OR oper OR concerto OR symphony OR sonata OR fugue OR motet OR saltarello OR organum OR ballade OR chanson OR laude OR madrigal OR pavane OR toccata OR cantata OR minuet OR partita OR sarabande OR sinfonia OR hymnes OR lied OR "music hall" OR quartet OR quintet OR requiem OR rhapsody OR scherzo OR "sinfonia concertante" OR waltz OR ballet OR zanger OR sangerin OR chanteur OR chanteuse OR cantante OR composer OR compositeur OR orchestra OR orchester OR orkester OR orchestre OR concierto OR konsert OR konzert OR koncert OR gramophone OR "record player" OR phonograph OR fonograaf OR fonograf OR grammofon OR skivspelare OR "wax cylinder" OR jukebox OR "cassette deck" OR "cassette player")) AND (provider_aggregation_edm_isShownBy:*)) OR ("gieddes samling") OR (musik AND DATA_PROVIDER:"Universitätsbibliothek Heidelberg") OR (antiphonal AND DATA_PROVIDER:"Bodleian Libraries, University of Oxford") OR (edm_datasetName:"2059208_Ag_EU_eSOUNDS_1020_CNRS-CREM") OR (title:(gradual OR antiphonal) AND edm_datasetName: "2021003_Ag_FI_NDL_fragmenta") NOT (DATA_PROVIDER:"Progetto ArtPast- CulturaItalia" OR DATA_PROVIDER:"Internet Culturale" OR DATA_PROVIDER:"Accademia Nazionale di Santa Cecilia" OR DATA_PROVIDER:"Regione Umbria" OR DATA_PROVIDER:"Regione Emilia Romagna" OR DATA_PROVIDER:"Regione Lombardia" OR DATA_PROVIDER:"Regione Piemonte" OR DATA_PROVIDER:"National Széchényi Library - Hungarian Electronic Library" OR DATA_PROVIDER:"Rijksdienst voor het Cultureel Erfgoed" OR DATA_PROVIDER:"Phonogrammarchiv - Österreichische Akademie der Wissenschaften; Austria" OR DATA_PROVIDER:"Ministère de la culture et de la communication, Musées de France" OR DATA_PROVIDER:"CER.ES: Red Digital de Colecciones de museos de España" OR DATA_PROVIDER:"MuseiD-Italia" OR DATA_PROVIDER:"Narodna biblioteka Srbije - National Library of Serbia" OR DATA_PROVIDER:"National and University Library in Zagreb" OR DATA_PROVIDER:"National Széchényi Library - Digital Archive of Pictures" OR DATA_PROVIDER:"Vast-Lab" OR DATA_PROVIDER:"Herzog August Bibliothek Wolfenbüttel" OR DATA_PROVIDER:"Centro de Documentación de FUNDACIÓN MAPFRE" OR PROVIDER:"OpenUp!" OR edm_datasetName:"9200123_Ag_EU_TEL_a1023_Sibiu" OR edm_datasetName:"2048319_Ag_EU_ApeX_NLHaNA" OR edm_datasetName:"2059202_Ag_EU_eSOUNDS_1004_Rundfunk" OR edm_datasetName:"09335_Ag_EU_Judaica_cfmj4" OR edm_datasetName:"09326_Ag_EU_Judaica_cfmj3" OR what:"opere d\'arte visiva" OR what:"operating rooms" OR what:"operating systems" OR what:"co-operation" OR what:operation)';

        $data = array(
            'query' => '*',
            'qf' => array(),
            'rows' => 1,
            'sort' => $sort.'+'.$sortOrder,
            'wt' => 'json',
            'wskey' => 'api2demo');

        if(strtoupper($type) == 'IMAGE') {
            $query = 'https://www.europeana.eu/api/v2/search.json?thumbnail=true&qf=TYPE:'.strtoupper($type).'&qf=COUNTRY:'.strtolower($country).'&'.http_build_query($data);
        } elseif(strtoupper($type) == 'TEXT') {
            $query = 'https://www.europeana.eu/api/v2/search.json?qf=TEXT_FULLTEXT:true&qf=TYPE:'.strtoupper($type).'&qf=COUNTRY:'.strtolower($country).'&'.http_build_query($data);
        } elseif(strtoupper($type) == 'ART') {
            $query = 'https://www.europeana.eu/api/v2/search.json?'.urlencode($art).'&qf=COUNTRY:'.strtolower($country).'&'.http_build_query($data);
        } elseif(strtoupper($type) == 'MUSIC') {
            $query = 'https://www.europeana.eu/api/v2/search.json?'.urlencode($music).'&qf=COUNTRY:'.strtolower($country).'&qf=SOUND_DURATION:very_short&qf=SOUND_DURATION:short&qf=SOUND_DURATION:medium&qf=SOUND_DURATION:long&qf=SOUND_HQ:true&'.http_build_query($data);
        }


        $this->log->log(date('Y/m/d h:i:s a', time())." -- Generating query", 'entity');
        $this->log->log(urldecode($query), 'entity');

        return $query;
    }

    public function agnosticAlgorithm($object)
    {
        $this->log->log("------------------------------------------------------------------", 'entity');
        $this->log->log("-> entity.similarItems->agnosticAlgorithm() -- ".date('Y/m/d h:i:s a', time()), 'entity');

        $object = (object) $object;
        $q = '';

        if($object->dctermsMedium != null) {
            $q .= "(".$this->stringify->stringify($object->dctermsMedium, ' AND ', true).")";
        }
        if($object->dcTitle != null) {
            $q .= "(".$this->stringify->stringify($object->dcTitle, ' AND ', true).")";
        }
        if($object->dcPublisher != null) {
            $q .= "who:(".$this->stringify->stringify($object->dcPublisher, ' AND ', true).")";
        }
        if($object->dcLanguage != null) {
            $q .= "(".$this->stringify->stringify($object->dcLanguage, ' AND ', true).")";
        }
        if($object->dcFormat != null) {
            $q .= "(".$this->stringify->stringify($object->dcFormat, ' AND ', true).")";
        }
        if($object->edmDatasetName != null) {
            $q .= "(".$this->stringify->stringify($object->edmDatasetName, ' AND ', true).")";
        }
        if($object->language != null) {
            $q .= "(".$this->stringify->stringify($object->language, ' AND ', true).")";
        }
        if($object->edmDataProvider != null) {
            $q .= "(".$this->stringify->stringify($object->edmDataProvider, ' AND ', true).")";
        }
        if($object->edmProvider != null) {
            $q .= "(".$this->stringify->stringify($object->edmProvider, ' AND ', true).")";
        }
        if($object->dcDescription != null) {
            $q .= "(".$this->stringify->stringify($object->dcDescription, ' AND ', true).")";
        }
        if($object->dcCreator != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "who:(".$this->stringify->stringify($object->dcCreator, ' AND ', true).")";
        }
        if($object->dcContributor != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "who:(".$this->stringify->stringify($object->dcContributor, ' AND ', true).")";
        }
        if($object->dcType != null) {
            $q .= "what:(".$this->stringify->stringify($object->dcType, ' OR ', true).")";
        }
        if($object->dcSubject != null) {
            $q .= "what:(".$this->stringify->stringify($object->dcSubject, ' OR ', true).")";
        }
        if($object->dctermsTemporal != null) {
            $q .= "when:(".$this->stringify->stringify($object->dctermsTemporal, ' OR ', true).")";
        }
        if($object->dctermsCreated != null) {
            $q .= "when:(".$this->stringify->stringify($object->dctermsCreated, ' OR ', true).")";
        }
        if($object->dcDate != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "when:(".$this->stringify->stringify($object->dcDate, ' OR ', true).")";
        }
        if($object->dctermsProvenance != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "where:(".$this->stringify->stringify($object->dctermsProvenance, ' AND ', true).")";
        }
        if($object->dctermsSpatial != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "where:(".$this->stringify->stringify($object->dctermsSpatial, ' AND ', true).")";
        }
        if($object->europeana_id != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "NOT europeana_id:\"".$this->stringify->stringify($object->europeana_id, ' AND ', true)."\"";
        }

        $data = array(
            'query' => $q,
            'rows' => 1,
            'wskey' => "api2demo");

        $this->log->log(date('Y/m/d h:i:s a', time())." -- Generating query", 'entity');
        $this->log->log(urldecode('https://www.europeana.eu/api/v2/search.json?'.http_build_query($data)), 'entity');

        return 'https://www.europeana.eu/api/v2/search.json?'.http_build_query($data);
    }

    public function typologicalAlgorithm($object)
    {
        $this->log->log("------------------------------------------------------------------", 'entity');
        $this->log->log("-> entity.similarItems->typologicalAlgorithm() -- ".date('Y/m/d h:i:s a', time()), 'entity');

        $object = (object) $object;
        $q = '';

        if($object->dcType != null) {
            $q .= "what:(".$this->stringify->stringify($object->dcType, ' OR ', true).")";
        }
        if($object->dcSubject != null) {
            $q .= "what:(".$this->stringify->stringify($object->dcSubject, ' OR ', true).")";
        }
        if($object->dcCreator != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "who:(".$this->stringify->stringify($object->dcCreator, ' OR ', true).")";
        }
        if($object->dcContributor != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "who:(".$this->stringify->stringify($object->dcContributor, ' OR ', true).")";
        }
        if($object->dctermsProvenance != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "where:(".$this->stringify->stringify($object->dctermsProvenance, ' OR ', true).")";
        }
        if($object->dctermsSpatial != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "where:(".$this->stringify->stringify($object->dctermsSpatial, ' OR ', true).")";
        }
        if($object->europeana_id != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "NOT europeana_id:\"".$this->stringify->stringify($object->europeana_id, ' OR ', true)."\"";
        }

        $data = array(
            'query' => $q,
            'rows' => 1,
            'wskey' => "api2demo");

        $this->log->log(date('Y/m/d h:i:s a', time())." -- Generating query", 'entity');
        $this->log->log(urldecode('https://www.europeana.eu/api/v2/search.json?'.http_build_query($data)), 'entity');

        return 'https://www.europeana.eu/api/v2/search.json?'.http_build_query($data);
    }

    public function chronologicalAlgorithm($object)
    {
        $this->log->log("------------------------------------------------------------------", 'entity');
        $this->log->log("-> entity.similarItems->chronologicalAlgorithm() -- ".date('Y/m/d h:i:s a', time()), 'entity');

        $object = (object) $object;
        $q = '';

        if($object->dctermsTemporal != null) {
            $q .= "when:(".$this->stringify->stringify($object->dctermsTemporal, ' OR ', true).")";
        }
        if($object->dctermsCreated != null) {
            $q .= "when:(".$this->stringify->stringify($object->dctermsCreated, ' OR ', true).")";
        }
        if($object->dcDate != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "when:(".$this->stringify->stringify($object->dcDate, ' OR ', true).")";
        }
        if($object->europeana_id != null) {
            if($q != "") { $q .= ' OR ';}
            $q .= "NOT europeana_id:\"".$this->stringify->stringify($object->europeana_id, ' OR ', true)."\"";
        }

        $data = array(
            'query' => $q,
            'rows' => 1,
            'wskey' => "api2demo");

        $this->log->log(date('Y/m/d h:i:s a', time())." -- Generating query", 'entity');
        $this->log->log(urldecode('https://www.europeana.eu/api/v2/search.json?'.http_build_query($data)), 'entity');

        return 'https://www.europeana.eu/api/v2/search.json?'.http_build_query($data);
    }
}
