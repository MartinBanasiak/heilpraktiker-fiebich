<?php
$dbName = 'dcshop';
$pdoDsn = 'mysql:dbname='.$dbName.';host=localhost;port=3306;charset=utf8mb4';
$pdo = new PDO($pdoDsn, 'root', '123');

$structTemplate = <<<EOT
package main

import (
 "fmt"
 "database/sql" 
 "github.com/jinzhu/gorm"
 _ "github.com/jinzhu/gorm/dialects/mysql"
 "time"
)

type {%StructName%} struct {
	{%Fields%}
}
EOT;

$restControllerTemplate = <<<EOT
package main

import (
 "fmt"
 "database/sql" 
 "github.com/gin-gonic/gin"
 _ "github.com/go-sql-driver/mysql"
 "github.com/jinzhu/gorm"
 _ "github.com/jinzhu/gorm/dialects/mysql"
 "time"
)

var db *gorm.DB
var err error

func main() {
    db, _ = gorm.Open("mysql", "root:123@tct(127.0.0.1:3306)/gotest?charset=utf8&parseTime=True&loc=Local")
    
    if err != nil {
        fmt.Println(err)
    }
    defer db.Close()
    
    db.AutoMigrate(&{%StructName%}{})
    
    r := gin.Default()
    r.GET("/{%EndpointName%}/", Get{%StructNamePlural%})
    r.GET("/{%EndpointName%}/:id", Get{%StructName%})
    r.POST("/{%EndpointName%}", Create{%StructName%})
    r.PUT("/{%EndpointName%}/:id", Update{%StructName%})
    r.DELETE("/{%EndpointName%}/:id", Delete{%StructName%})
    
    r.Run(":8080")
}

func Get{%StructNamePlural%}(c *gin.Context) {
    var models []{%StructName%}
    if err := db.Find(&models).Error; err != nil {
        c.AbortWithStatus(404)
        fmt.Println(err)
    } else {
        c.JSON(200, models)
    }

}

func Get{%StructName%}(c *gin.Context) {
    id := c.Params.ByName("id")
    var model {%StructName%}
    if err := db.Where("id = ?", id).First(&model).Error; err != nil {
        c.AbortWithStatus(404)
        fmt.Println(err)
    } else {
        c.JSON(200, model)
    }
}

func Create{%StructName%}(c *gin.Context) {
    var model {%StructName%}
    c.BindJSON(&model)

    db.Create(&model)
    c.JSON(200, model)
}

func Update{%StructName%}(c *gin.Context) {
    var model {%StructName%}
    id := c.Params.ByName("id")

    if err := db.Where("id = ?", id).First(&model).Error; err != nil {
        c.AbortWithStatus(404)
        fmt.Println(err)
    }
    c.BindJSON(&model)

    db.Save(&model)
    c.JSON(200, model)
}

func Delete{%StructName%}(c *gin.Context) {
    id := c.Params.ByName("id")
    var model {%StructName%}
    d := db.Where("id = ?", id).Delete(&model)
    fmt.Println(d)
    c.JSON(200, gin.H{"id #" + id: "deleted"})
}


EOT;

$structSearches = [
    '{%StructName%}',
    '{%Fields%}',
];

$restControllerSearches = [
    '{%StructName%}',
    '{%StructNamePlural%}',
    '{%EndpointName%}',
];


$fieldTemplate = <<<EOT
	{%CamelName%} {%Type%} `db:"{%SnakeName%}" json:"{%SnakeName%}" form:"{%SnakeName%}"`

EOT;
$fieldTemplateSearches = [
    '{%CamelName%}',
    '{%Type%}',
    '{%SnakeName%}'
];


$queryTables = '
	SELECT
		`TABLE_NAME`
	FROM
		`information_schema`.`tables`
	WHERE
		    `TABLE_SCHEMA` = :dbname
	    AND `TABLE_TYPE` != \'VIEW\'
';

$queryColumns = '
  SELECT
      `column_name`,
	  `column_type`,
	  `is_nullable`
  FROM
      `information_schema`.`columns`
  WHERE
      `table_name`=:tname';

$stmtTables = $pdo->prepare($queryTables);
$stmtTables->bindValue(':dbname',$dbName);

$basePath = __DIR__ . '../dc/go_microservices/';
if ($stmtTables->execute()) {
    $tableResultArray = $stmtTables->fetchAll(PDO::FETCH_ASSOC);
    if (is_array($tableResultArray)) {
        foreach ($tableResultArray as $table) {
            $restControllerString = '';
            $tableName = $table['TABLE_NAME'];
            echo "Working on table [$tableName]..." . PHP_EOL;
            $structName = snakeToCamelCase($tableName);
            $structNamePlural = $structName . 's';
            $endpointName = $tableName;

            $stmtColumns = $pdo->prepare($queryColumns);
            $stmtColumns->bindValue(':tname',$tableName);

            $destFileNameStruct = $tableName . '.go';
            $destFileNameRestController = $tableName . '_controller.go';

            if ($stmtColumns->execute()) {
                $columnResultArray = $stmtColumns->fetchAll(PDO::FETCH_ASSOC);
                if (is_array($columnResultArray)) {
                    $fieldsString = '';
                    foreach ($columnResultArray as $columnInfo) {
                        $columnNameSnake = $columnInfo['column_name'];
                        $locTemplate = $fieldTemplate;
                        if ($columnNameSnake === 'id') {
                            $locTemplate = '{%CamelName%} {%Type%} `db:"{%SnakeName%}" json:"{%SnakeName%}" form:"{%SnakeName%}" sql:"AUTO_INCREMENT" gorm:"primary_key"`';
                        }
                        echo "integrating column [$columnNameSnake]" . PHP_EOL;
                        $columnNameCamel = snakeToCamelCase($columnNameSnake);
                        $columnType = $columnInfo['column_type'];
                        $isNullable = $columnInfo['is_nullable'] === 'YES';
                        $goTypeString = mapMySQLTypeToGoType($columnType,$isNullable);
                        $fieldReplacements = [$columnNameCamel,$goTypeString,$columnNameSnake];
                        $currFieldString = str_replace($fieldTemplateSearches, $fieldReplacements, $fieldTemplate);
                        $fieldsString .= $currFieldString;
                    }
                    $structString = str_replace($structSearches,[$structName,$fieldsString],$structTemplate);
                    $destFilePathStruct = $basePath . $destFileNameStruct;
                    if (is_writable($basePath)) {
                        file_put_contents($destFilePathStruct, $structString);
                    } else {
                        throw new ErrorException('Cannot write to path [' . $destFilePathStruct . '] - base path [' . $basePath . '] not writable.');
                    }

                    $restControllerString = str_replace($restControllerSearches, [$structName,$structNamePlural,$endpointName], $restControllerTemplate);
                    $destFilePathRestController = $basePath . $destFileNameRestController;
                    if (is_writable($basePath)) {
                        file_put_contents($destFilePathRestController, $restControllerString);
                    } else {
                        throw new ErrorException('Cannot write to path [' . $destFilePathRestController . '] - base path [' . $basePath . '] not writable.');
                    }
                }
            }
        }
    }
}

function mapMySQLTypeToGoType($mysqlType,$isNullable) {
    $goTypeString = '';
    if (isMysqlTypeInt($mysqlType)) {
        $goTypeString = !$isNullable ? 'int64':'sql.NullInt64';
    } elseif (isMysqlTypeVarchar($mysqlType) || isMysqlTypeText($mysqlType)) {
        $goTypeString = !$isNullable ? 'string':'sql.NullString';
    } elseif (isMysqlTypeDecimal($mysqlType)) {
        $goTypeString = !$isNullable ? 'float64' : 'sql.NullFloat64';
    } elseif (isMysqlTypeDate($mysqlType) || isMysqlTypeTime($mysqlType) || isMysqlTypeDateTime($mysqlType) || isMysqlTypeTimestamp($mysqlType)) {
        $goTypeString = !$isNullable ? 'time.Time' : 'NullTime';
    } elseif (isMysqlTypeBoolean($mysqlType)) {
        $goTypeString = !$isNullable ? 'bool' : 'sql.NullBool';
    } else {
        throw new InvalidArgumentException('MySQL Type [' . $mysqlType . '] not mapped!');
    }
    return $goTypeString;

}

function isMysqlTypeInt($mysqlType) {
    return stripos($mysqlType,'int') === 0 || stripos($mysqlType,'bigint') === 0;
}

function isMysqlTypeDecimal($mysqlType) {
    return stripos($mysqlType,'decimal') === 0 || stripos($mysqlType,'double') === 0;
}

function isMysqlTypeVarchar($mysqlType) {
    return stripos($mysqlType,'varchar') === 0 || stripos($mysqlType,'char') === 0;
}

function isMysqlTypeDate($mysqlType) {
    return stripos($mysqlType,'date') === 0;
}

function isMysqlTypeTime($mysqlType) {
    return stripos($mysqlType,'time') === 0;
}

function isMysqlTypeDateTime($mysqlType) {
    return stripos($mysqlType,'datetime') === 0;
}

function isMysqlTypeTimestamp($mysqlType) {
    return stripos($mysqlType,'timestamp') === 0;
}

function isMysqlTypeBoolean($mysqlType) {
    return stripos($mysqlType,'tinyint') === 0 || stripos($mysqlType,'boolean') === 0;
}

function isMysqlTypeText($mysqlType) {
    return stripos($mysqlType,'text') === 0 || stripos($mysqlType,'mediumtext') === 0 || stripos($mysqlType,'blob') !== false;
}

function snakeToCamelCase($name) {
    return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
}

