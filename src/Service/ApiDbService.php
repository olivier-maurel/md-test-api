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
        $this->em           = $em;
    }

    /**
	* Retourne les données récupérées dans l'API
	*
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
	* Genère les fichiers JSON et CSV de chaque tables necessaires
	*
	* @param array $data
	* @return bool
	*/
    public function generateJsonToCsv($data)
    {
        // Récupère les données et les convertient en format JSON
        $this->dataToJson($data);

        // Récupère les chemins des fichiers JSON créés
        $files = glob($this->publicPath.'/'.$this->filesFolder.'*.json');

        // Convertit chaques fichiers JSON en format CSV
        foreach ($files as $key => $file) {
            $exp = explode('/', $file);
            $filename = str_replace('.json', '', $exp[count($exp)-1]);
            $this->jsonToCSV($this->filesFolder.$filename.'.json', $this->filesFolder.$filename.'.csv');
        }
        
        return true;
    }

    /**
	* Insert dans la base de donnée les fichiers CSV
	*
	* @return bool
	*/
    public function insertToDatabase($data)
    {
        $files = glob($this->publicPath.'/'.$this->filesFolder.'*.csv');
        dump($this->dataToJson($data));exit;

        foreach ($files as $key => $value) {
            $exp        = explode('/', $value);
            $filename   = str_replace('.csv', '', $exp[count($exp)-1]);
            $value      = file($value);



            dump(floatval('1765.654')); exit;

            if (count($value) > 1) {

                $headers = explode(',',$value[0]);
                $content = explode(',',$value[1]);
                dump($headers);
                dump($content);
                
                $columns = '';
                $index   = 0;

                foreach ($headers as $k => $v) {
                    $columns = $columns.$v." ".gettype($content[$index]).",\n";
                    $index++;
                }

                $sql = 'CREATE TABLE '.$filename.'('.$columns.')';
                

            }
        }
        
        
        dump($headers, $content);
        exit;

        $sql = "
            CREATE TABLE
        ";


        $conn = $this->em->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        var_dump($stmt->fetchAll());die;
        
        
        $exec = system('php ../bin/console doctrine:query:sql '.$sql);
        dump($exec);
        exit;
        return false;
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
                                        $materiel->$key[0]->$k->$name = null;
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
        dump($dataClear); exit;
        $f = fopen($this->filesFolder.'materiels.json', 'w');
        fwrite($f, json_encode($data));
        fclose($f);

        return true;
    }

    /**
	* Récupère les fichiers JSON et créer les fichiers CSV
	*
	* @param array $data
	* @return bool
	*/
    private function jsonToCSV($jfilename, $cfilename)
    {
        if (($json = file_get_contents($jfilename)) == false)
            die('Error reading json file...');
        $data = json_decode($json, true);
        $fp = fopen($cfilename, 'w');
        $header = false;
        foreach ($data as $row)
        {
            if (empty($header))
            {
                $header = array_keys($row);
                fputcsv($fp, $header);
                $header = array_flip($header);
            }
            fputcsv($fp, array_merge($header, $row));
        }
        fclose($fp);
        return;
    }

    private function multipleArrayUnique($array)
    {
      $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
    
      foreach ($result as $key => $value)
      {
        if ( is_array($value) )
        {
          $result[$key] = $this->multipleArrayUnique($value);
        }
      }
    
      return $result;
    }
}