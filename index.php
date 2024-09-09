<?php 
// Require Composer's autoloader.
require 'vendor/autoload.php';
 
// Using Medoo namespace.
use Medoo\Medoo;

echo 'Get Data from file(API):';
// начальные настроки подключения к БД
$database = new Medoo([
	// [required]
	'type' => 'mysql',
	'host' => 'localhost',
	'database' => 'test',
	'username' => 'root',
	'password' => '',
 	]);

    
// создать таблицы
$database->create('users',
	[ "id"=>["INT", "NOT NULL", "AUTO_INCREMENT", "PRIMARY KEY"],
	"name"=> ["VARCHAR(30)"],
	"username"=>["VARCHAR(30)"],
	"email"=>["VARCHAR(50)"],
	"address_id"=>["INT"],
	"company_id"=>["INT"]
	]);

$database->create('address',[
	"id"=>["INT", "NOT NULL", "AUTO_INCREMENT", "PRIMARY KEY"],
	"street"=>["VARCHAR(50)"],
	"suite"=>["VARCHAR(50)"],
	"city"=>["VARCHAR(30)"]
]);

$database->create('company',[
	"id"=>["INT", "NOT NULL", "AUTO_INCREMENT", "PRIMARY KEY"],
	"name"=>["VARCHAR(30)"],
	"catchphrase"=>["VARCHAR(100)"],
	"bs"=>["VARCHAR(100)"]

]);

// создать класс, через который получим данные по API
class Creator{

	      public function getData(){
		      $file = file_get_contents('./users.json'); 
		       return json_decode($file);
	      }
	   
}

$creator = new Creator();
$array =  $creator -> getData();
echo '<pre>';
print_r($array);
echo '</pre>';

// записать полученные данные по API в таблицы
foreach($array as $key=>$item){
       $database->insert('address',[
       		'street'=> $item->address->street,
		'suite'=> $item->address->suite,
		'city'=> $item->address->city
        ]);
		$address_id = $database->id();       

	$database->insert("company",[
       		'name'=> $item->company->name,
		'catchphrase'=> $item->company->catchPhrase,
		'bs'=> $item->company->bs
       ]);
       $company_id = $database->id();

	$database->insert("users",[
		'name'=> $item->name,
		'username'=> $item->username,
		'email'=> $item->email,
		'address_id'=> $address_id,
		'company_id'=> $company_id
	]);

}

?>
