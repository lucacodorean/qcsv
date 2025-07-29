# qcsv

### A tool used to work with tabular data.

#### Prepending a header
```php
php ../qcsv.php --source=INPUT_STREAM --command=prepend --destination=DESTINATION_STREAM --options=new_headers[]
```
#### Indexing a data table
```php
php ../qcsv.php --source=INPUT_STREAM --command=index --destination=DESTINATION_STREAM
```
#### Format date-time columns to a given format
```php
php ../qcsv.php --source=INPUT_STREAM --command=format --destination=DESTINATION_STREAM --options=Y/m/d**
```
#### Remove a column from the data table
```php
php ../qcsv.php --source=INPUT_STREAM --command=remove --destination=DESTINATION_STREAM --options=column**
```
#### Reorder columns in the data table
```php
php ../qcsv.php --source=INPUT_STREAM --command=reorder --destination=DESTINATION_STREAM --options=column**
```
#### Truncate columns from the data table
```php
php ../qcsv.php  --command=truncate --options=COLUMN --options=TRUNCATE_LENGHT
```
#### Encrypt & Decrypt columns using Asymmetric Encryption
```php
 php ../qcsv.php --source=INPUT_STREAM --command=encrypt --destination=DESTINATION_STREAM --options=COLUMNS[] --public_key_path=PUBLIC_KEY_ORIGIN_STREAM

```
```php
php ../qcsv.php --source=INPUT_STREAM --command=decrypt --destination=DESTINATION_STREAM--options=COLUMNS[] --private_key_path=PRIVATE_KEY_ORIGIN_STREAM
```
#### Sign and verify the signature based on columns of the data table
```php
php ../qcsv.php --source=INPUT_STREAM --command=verify --options=DATA[] --private_key_path=PRIVATE_KEY_ORIGIN_STREAM
```
```php
php ../qcsv.php --source=INPUT_STREAM --command=sign --options=DATA[] --public_key_path=PUBLIC_KEY_ORIGIN_STREAM
```

#### Joining 
```php
php qcsv.php --command=join --options=public/wages.csv --options=id,id_employee | 
```
#### Selecting
```php
php qcsv.php --command=select --options=id,email,age --options=wage,GREATER_THAN,300
```

#### Piping operations
```php
php ../qcsv.php --source=../public/employees.csv --command=remove  --options=employee | php ../qcsv.php  --command=format --options=Y/m/d

php qcsv.php --source=public/employees.csv --command=remove  --options=employee | 
php qcsv.php  --command=format --options=Y/m/d | 
php qcsv.php --command=join --options=public/wages.csv --options=id,id_employee | 
php qcsv.php --command=select --options=id,email,age --options=wage,GREATER_THAN,300

```
