<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;


class ApiDbService
{
    public function __construct(
            HttpClientInterface $client,
            ContainerInterface $container,
            EntityManagerInterface $em
        )
    {
        $this->client       = $client;
		$this->apiUrl		= $container->getParameter('api.url');
		$this->apiToken		= $container->getParameter('api.token');
		$this->filesFolder	= $container->getParameter('files.folder');
		$this->publicPath	= $container->getParameter('path.public');
		$this->absolutePath	= $container->getParameter('path.absolute');
        $this->em           = $em;
    }

    /**
	* Retourne les données récupérées dans l'API
	* @return array
	*/
    public function getApiData()
    {
        // Déclaration des variables
        $array      = [];
        $lastPage   = 1;
        $i          = 1;

        // Génère une URL dynamique.
        $urlGenerate = function($parameter = 'materiel', $search = null, $page = '1'){
            return $this->apiUrl.$parameter.'?search='.$search.'&catalogues[]=beproactiv&page='.$page.'&token='.$this->apiToken;   
        };
        
        // Récupère les valeurs par page si la limite est trop faible.
        while ($i <= $lastPage) {
            $response   = $this->client->request('GET',$urlGenerate('materiels',null,$i));
            $data       = json_decode($response->getContent());
            $array      = array_merge($array, $data->data);
            $lastPage   = $data->last_page;
            $i++;
        }

        return $array;
    }

    /**
    * Insert dans la base de donnée les fichiers CSV
	* @return bool
	*/
    public function insertToDatabase($data)
    {
        //////////////// SUPPRIME AUTOMATIQUE LA BDD AU LANCEMENT
        $drop = "DROP TABLE `caracteristiques`, `catalogue`, `fabricant`, `grandeur`, `materiels`, `metier`, `references`, `tarifs`, `tarifs_grandeurs`, `type`";
        $conn = $this->em->getConnection();
        $stmt = $conn->prepare($drop);
        try{
            $stmt->execute();
        }catch(\Throwable $th){
            dump('no drop db');
        }
        ////////////////
        
        $sql['create']  = '';
        $sql['insert']  = [];
        $data           = $this->dataToJson($data);
        $length         = [
            'integer' => '(10)',
            'varchar' => '(255)',
            'double'  => '(8,4)',
            'boolean' => ''
        ];
        $index   = -1;
        $counter = 9;
        $columns = '';
        
        // Trie et stock dans un tableau les données d'insertion
        foreach ($data as $key => $value) {
            foreach ($value as $ind => $val) {
                $content = '';
                $headers = '';
                foreach ($val as $i => $v) {
                    $type = gettype($v);
                    if (gettype($v) == 'NULL') {
                        if ( substr($i, strlen($i)-2, strlen($i)) == 'id')
                        $type = 'integer';
                        else $type = 'boolean';
                    } elseif (gettype($v) == 'string')
                    $type = 'varchar';
                    $headers = $headers."`".$i."`,";
                    if($v == '' || $v == '[]') {
                        $v = 'NULL';
                        $type = 'varchar';
                    } elseif($type == 'varchar')
                    $v = "'".$v."'";
                    $col[$key]["`".$i."`"] = $type.$length[$type].",";
                    $content = $content.$v.",";
                }
                if ($counter > 8) {
                    $counter = 0;
                    $index++;
                }
                $sql['insert'][$index][] = 'INSERT INTO `'.$key."`(".rtrim($headers, ",").') VALUE ('.rtrim($content, ",").');';
                $counter++;   
            }
        }
        
        // Trie et stock dans un tableau les données de création
        foreach ($col as $key => $value) {
            $value = (array_unique($value, SORT_REGULAR));
            $primary = '`id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,';
            foreach ($col[$key] as $k => $v) {
                if ("`".rtrim($key,'s')."_id`" == $k || "`".$key."_id`" == $k)
                $primary = $k." ".( rtrim($v,",")." PRIMARY KEY NOT NULL," );
                else $columns = $columns.$k.' '.$v;
            }
            $sql['create'] = $sql['create'].'CREATE TABLE `'.$key."`(".$primary.rtrim($columns, ",").');';
            $columns = '';
        }
        
        
        $this->createData($sql);
        $this->insertData($sql);
        $this->importSqlFile($this->publicPath.'/assets/files/foreign_keys.sql');
        $this->generateEntities();
        // php bin/console doctrine:mapping:import "App\Entity" annotation --path=src/Entity
        
        exit;
        return false;
    }
    
    // Création des tables dans la base de donnée.
    public function createData($sql)
    {
        $conn = $this->em->getConnection();
        $stmt = $conn->prepare($sql['create']);  
        $stmt->execute();
        
        return [
            'success' => true,
            'message' => 'Création des tables de données: OK'
        ];
    }
    
    // Ajout des données dans la base de donnée.
    public function insertData($sql)
    {
        $conn = $this->em->getConnection();

        foreach ($sql['insert'] as $index => $value) {
            $value = (array_unique($value, SORT_REGULAR));

            foreach ($value as $k => $v) {
                $stmt = $conn->prepare($v);
                
                try {

                    $stmt->execute();
                
                } catch(\Throwable $e) {
                    
                    if ($e->getPrevious()->getCode() == "23000") {
                        
                        $start  = str_replace('INSERT INTO','UPDATE',explode('(',$v)[0]);
                        $col    = explode(',', explode('(', explode(')',$v)[0])[1] );
                        $val    = explode(',', explode('(', explode(')',$v)[1])[1] );
                        $v      = $start.' SET ';
                        
                        foreach ($col as $key => $value) {
                            
                            if ($value === "`".explode('`',$start)[1]."_id`")
                                $where = $val[$key];
                            
                            $v = $v.$value.' = '.$val[$key].',';
                        }
                        
                        $v = rtrim($v, ',').' WHERE `'.explode('`',$start)[1].'_id` = '.$where;
                        $stmt = $conn->prepare($v);
                        $stmt->execute();        
                    }
                }
            }
        }
        
        return [
            'success' => true,
            'message' => 'Insertion des données: OK'
        ];
    }
    
    // Exécute un fichier SQL.
    public function importSqlFile($path)
    {
        $conn   = $this->em->getConnection();
        $lines  = file($path);
        
        foreach ($lines as $key => $value) {
            $stmt = $conn->prepare($value);
            $stmt->execute();
        }
        
        return [
            'success' => true,
            'message' => 'Gestion des clés étrangères: OK'
        ];
    }
    
    // Génère les entités depuis la base de donnée.
    public function generateEntities()
    {
        $exe = 'nohup /usr/bin/php ../bin/console doctrine:mapping:import "App\Entity" annotation --path=../src/Entity 2>&1 &';
        shell_exec($exe);
        
        return [
            'success' => true,
            'message' => 'Génération des entités: OK'
        ];
    }
    
    /**
     * Récupère les données et créer les fichiers JSON
     *
     * @param array $data
     * @return bool
     */
    private function dataToJson($data)
    {
        $dataClear = [];
        foreach ($data as $materiel) {
            foreach ($materiel as $key => $val) {
                if(gettype($val) == 'array'){
                    if (count($val) > 0) {
                        foreach ($val[0] as $k => $v) {
                            if (gettype($v) == 'object') {
                                foreach ($v as $name => $w) {
                                    if (gettype($w) == 'array')
                                    $materiel->$key[0]->$k->$name = '[]';
                                }
                                $dataClear[$k][] = (array)$v;
                                $dataClear[$k] = array_values($this->multipleArrayUnique($dataClear[$k]));
                                $f = fopen($this->filesFolder.$k.'.json', 'w');
                                fwrite($f, json_encode([$v]));
                                fclose($f);
                                unset($materiel->$key[0]->$k);
                            }
                        } 
                        $dataClear[$key][] = (array)$val[0];
                        $dataClear[$key] = array_values($this->multipleArrayUnique($dataClear[$key]));
                        $f = fopen($this->filesFolder.$key.'.json', 'w');
                        fwrite($f, json_encode([$val[0]]));
                        fclose($f);
                        unset($materiel->$key);
                    }
                    else $materiel->$key = null;
                } elseif (gettype($val) == 'object') {
                    foreach ($val as $k => $v) {
                        if (gettype($v) == 'object') {
                            $dataClear[$k][] = (array)$v;
                            $dataClear[$k] = array_values($this->multipleArrayUnique($dataClear[$k]));
                            $f = fopen($this->filesFolder.$k.'.json', 'w');
                            fwrite($f, json_encode([$v]));
                            fclose($f);
                            unset($materiel->$key->$k);
                        }
                    }
                    $dataClear[$key][] = (array)$val;
                    $dataClear[$key] = array_values($this->multipleArrayUnique($dataClear[$key]));
                    $f = fopen($this->filesFolder.$key.'.json', 'w');
                    fwrite($f, json_encode([$val]));
                    fclose($f);
                    unset($materiel->$key);
                }
                
            }  
        }
        $dataClear['materiels'] = $data;
        $f = fopen($this->filesFolder.'materiels.json', 'w');
        fwrite($f, json_encode($data));
        fclose($f);
        
        return $dataClear;
    }

    private function multipleArrayUnique($array)
    {
        $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
        
        foreach ($result as $key => $value)
        {
            if (is_array($value))
            {
                $result[$key] = $this->multipleArrayUnique($value);
            }
        }

        return $result;
    }
    
    // /**
    // * Genère les fichiers JSON et CSV de chaque tables necessaires
    // *
    // * @param array $data
    // * @return bool
    // */
    // public function generateJsonToCsv($data)
    // {
    //     // Récupère les données et les convertient en format JSON
    //     $this->dataToJson($data);
    
    //     // Récupère les chemins des fichiers JSON créés
    //     $files = glob($this->publicPath.'/'.$this->filesFolder.'*.json');
    
    //     // Convertit chaques fichiers JSON en format CSV
    //     foreach ($files as $key => $file) {
    //         $exp = explode('/', $file);
    //         $filename = str_replace('.json', '', $exp[count($exp)-1]);
    //         $this->jsonToCSV($this->filesFolder.$filename.'.json', $this->filesFolder.$filename.'.csv');
    //     }
        
    //     return true;
    // }

    // /**
	// * Récupère les fichiers JSON et créer les fichiers CSV
	// *
	// * @param array $data
	// * @return bool
	// */
    // private function jsonToCSV($jfilename, $cfilename)
    // {
        //     if (($json = file_get_contents($jfilename)) == false)
        //         die('Error reading json file...');
        //     $data = json_decode($json, true);
        //     $fp = fopen($cfilename, 'w');
        //     $header = false;
        //     foreach ($data as $row)
        //     {
            //         if (empty($header))
            //         {
                //             $header = array_keys($row);
                //             fputcsv($fp, $header);
                //             $header = array_flip($header);
                //         }
                //         fputcsv($fp, array_merge($header, $row));
                //     }
                //     fclose($fp);
                //     return;
                // }
    
}