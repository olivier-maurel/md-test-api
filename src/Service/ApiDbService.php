<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ApiDbService
{
    public function __construct(
            HttpClientInterface $client,
            ContainerInterface $container
        )
    {
        $this->client       = $client;
		$this->apiUrl		= $container->getParameter('api.url');
		$this->apiToken		= $container->getParameter('api.token');
		$this->filesFolder	= $container->getParameter('files.folder');
		$this->publicPath	= $container->getParameter('path.public');
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
    public function insertToDatabase()
    {
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
                                $f = fopen($this->filesFolder.$k.'.json', 'w');
                                fwrite($f, json_encode([$v]));
                                fclose($f);
                                unset($materiel->$key[0]->$k);
                            }
                        }
                        $f = fopen($this->filesFolder.$key.'.json', 'w');
                        fwrite($f, json_encode([$val[0]]));
                        fclose($f);
                        unset($materiel->$key);
                    }
                    else $materiel->$key = null;
                } elseif (gettype($val) == 'object') {
                    foreach ($val as $k => $v) {
                        if (gettype($v) == 'object') {
                            $f = fopen($this->filesFolder.$k.'.json', 'w');
                            fwrite($f, json_encode([$v]));
                            fclose($f);
                            unset($materiel->$key->$k);
                        }
                    }
                    $f = fopen($this->filesFolder.$key.'.json', 'w');
                    fwrite($f, json_encode([$val]));
                    fclose($f);
                    unset($materiel->$key);
                }
            }  
        }
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
}