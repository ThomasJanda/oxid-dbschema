<?php

namespace rs\dbschema\Application\Controller\Admin;

use oxdb;
use OxidEsales\Eshop\Core\Request;

class rs_dbschema_ide extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    protected $_sThisTemplate="rs_dbschema_ide.tpl";


    protected $_saveFolder = "/../../../dbschemaprojects";

    protected $_relations=array(

        array(
            //primary key
            'from_table' => 'oxarticles',
            'from_column' => 'oxid',
            //foreign key
            'to_table' => 'oxartextends',
            'to_column' => 'oxid',
            'relation' => "1:1",
        ),
        array(
            //primary key
            'from_table' => 'oxarticles',
            'from_column' => 'oxid',
            //foreign key
            'to_table' => 'oxarticles',
            'to_column' => 'oxparentid',
        ),
        array(
            //primary key
            'from_table' => 'oxobject2category',
            'from_column' => 'oxcatnid',
            //foreign key
            'to_table' => 'oxcategories',
            'to_column' => 'oxid',
        ),
        array(
            //primary key
            'from_table' => 'oxobject2category',
            'from_column' => 'oxobjectid',
            //foreign key
            'to_table' => 'oxarticles',
            'to_column' => 'oxid',
        ),

        array(
            //primary key
            'from_table' => 'oxobject2attribute',
            'from_column' => 'oxobjectid',
            //foreign key
            'to_table' => 'oxarticles',
            'to_column' => 'oxid',
        ),
        array(
            //primary key
            'from_table' => 'oxobject2attribute',
            'from_column' => 'oxattrid',
            //foreign key
            'to_table' => 'oxattribute',
            'to_column' => 'oxid',
        ),
    );


    /**
     * save project
     */
    public function save()
    {
        $request = oxNew(Request::class);

        $filename = $request->getRequestEscapedParameter('filename');
        $table = $request->getRequestEscapedParameter('table');
        $left = $request->getRequestEscapedParameter('left');
        $top = $request->getRequestEscapedParameter('top');
        $width = $request->getRequestEscapedParameter('width');
        $height = $request->getRequestEscapedParameter('height');

        $data = array();
        for($x=0;$x<count($table);$x++)
        {
            $data[$table[$x]]['title']=$table[$x];
            $data[$table[$x]]['left']=$left[$x];
            $data[$table[$x]]['top']=$top[$x];
            $data[$table[$x]]['width']=$width[$x];
            $data[$table[$x]]['height']=$height[$x];
        }
        $path = __DIR__.$this->_saveFolder."/".$filename;
        file_put_contents($path,json_encode($data));
        die("");
    }

    /**
     * load a project
     */
    public function load()
    {
        $request = oxNew(Request::class);

        $filename = $request->getRequestEscapedParameter('filename');

        $path = __DIR__.$this->_saveFolder."/".$filename;
        if(file_exists($path))
        {
            $data = file_get_contents($path);
            $data = json_decode($data, true);
            $this->_aViewData['tables']=$data;
            $this->_sThisTemplate="inc/rs_dbschema_addtables.tpl";
        }
        else
        {
            die("");
        }
    }

    /**
     * get all projectfiles back that present
     */
    public function getprojectfiles()
    {
        $list = array();
        $path = __DIR__.$this->_saveFolder;

        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && !is_dir($path."/".$entry)) {

                    if(substr($entry,strlen($entry)- strlen('.cpfdb'))=='.cpfdb')
                        $list[] = $entry;
                }
            }
            closedir($handle);
        }

        natsort($list);

        echo json_encode($list);
        die("");
    }

    /**
     * return all relations between the tables
     */
    public function getrelations()
    {
        $list=[];
        $request = oxNew(Request::class);

        $tables = $request->getRequestEscapedParameter('tables');

        if(is_array($tables)) {

            $definition = array();
            foreach ($tables as $table) {
                $definition[$table] = $this->getColumns($table);
            }

            foreach ($definition as $table => $columns) {
                foreach ($columns as $coldef) {
                    if ($coldef['foreign']) {
                        if (in_array($coldef['foreign_table'], $tables)) {

                            $sRelation=$coldef['relation']??'1:n';
                            $aRelation = explode(":",$sRelation);

                            $item = [];
                            $item['from_table'] = $coldef['foreign_table'];
                            $item['from_column'] = $coldef['foreign_column'];
                            $item['from_text'] = $aRelation[0];
                            $item['to_table'] = $table;
                            $item['to_column'] = $coldef['title'];
                            $item['to_text'] = $aRelation[1];
                            $list[] = $item;
                        }
                    }
                }
            }

            /*
            $relations = $this->_relations;
            foreach($relations as $relation)
            {
                if(in_array($relation['from_table'],$tables) && in_array($relation['to_table'],$tables))
                {
                    $list[]=$relation;
                }
            }
            */
        }
        $this->_aViewData['relations']=$list;

        $this->_sThisTemplate="inc/rs_dbschema_addrelations.tpl";
    }

    /**
     * add a table to the project
     */
    public function addtable()
    {
        $item=[];

        $request = oxNew(Request::class);

        $item['title']=$request->getRequestEscapedParameter('table');
        $item['top']=$request->getRequestEscapedParameter('top');
        $item['left']=$request->getRequestEscapedParameter('left');
        $tables[]=$item;

        $this->_aViewData['tables']=$tables;
        $this->_sThisTemplate="inc/rs_dbschema_addtables.tpl";
    }

    public function refreshtablelist()
    {
        $this->_sThisTemplate="inc/rs_dbschema_tablelist.tpl";
    }

    /**
     * return a valid list of tablenames
     * @return array
     */
    public function getTables()
    {
        $request = oxNew(Request::class);
        $phrase = $request->getRequestEscapedParameter('phrase');

        $list=array();
        $sql="SELECT table_name
        FROM INFORMATION_SCHEMA.TABLES 
        where table_schema='".$this->getConfig()->getConfigParam('dbName')."'
        and table_type='BASE TABLE'
        order by table_name";
        $rs=oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->select($sql);
        if($rs!=false && $rs->count() > 0 )
        {
            while (!$rs->EOF) {

                $table = $rs->fields['table_name'];

                //not allow columns that end with e.g. 20160213
                if(!is_numeric(substr($table,strlen($table)-8))) {

                    if(strpos($table,$phrase)!==false || $phrase=="")
                    {
                        $list[]=$table;
                    }
                }
                $rs->fetchRow();
            }
        }
        return $list;
    }

    /**
     * return the comment assign to this table
     *
     * @param $table
     *
     * @return null|string
     */
    public function getTableComment($table)
    {
        $sql="SELECT table_comment
        FROM information_schema.tables
        WHERE table_schema='".$this->getConfig()->getConfigParam('dbName')."' 
        AND lower(table_name) = lower('".$table."')";
        //echo $sql;
        return oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne($sql);
    }

    /**
     * return all comments to a present table
     *
     * @param $table
     *
     * @return array
     */
    public function getColumns($table)
    {
        $list=array();
        $sql="select 
        lower(column_name) as Field,
        data_type as Type,
        COLUMN_COMMENT,
        COLUMN_KEY as 'Key',
        column_type
        FROM INFORMATION_SCHEMA.COLUMNS 
        where lower(table_name)=lower('".$table."') 
        and table_schema='".$this->getConfig()->getConfigParam('dbName')."'";
        //$sql="SHOW COLUMNS FROM ".$table;
        $rs=oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->select($sql);
        if($rs!=false && $rs->count() > 0 )
        {
            while (!$rs->EOF) {

                $field = $rs->fields['Field'];

                //not allow columns that end with e.g. 20160213
                if(!is_numeric(substr($field,strlen($field)-8)))
                {
                    $item=[];
                    $item['title']=$field;
                    $item['comment']=$rs->fields['COLUMN_COMMENT'];
                    $item['type']=$rs->fields['Type'];

                    if($item['type']=="enum")
                    {
                        $item['type2']=$rs->fields['column_type'];
                    }

                    $item['primary']=($rs->fields['Key']=="UNI" || $rs->fields['Key']=="PRI"?true:false);
                    $item['foreign']=false;


                    $sSql="SELECT 
                    count(*)
                    FROM INFORMATION_SCHEMA.`KEY_COLUMN_USAGE`
                    WHERE REFERENCED_TABLE_NAME is not null
                    and REFERENCED_COLUMN_NAME is not null
                    and TABLE_SCHEMA='".$this->getConfig()->getConfigParam('dbName')."'
                    and lower(table_name)=lower('$table')
                    and lower(COLUMN_NAME)=lower('$field')";
                    if(oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne($sSql)=="1")
                    {

                        $sSql="SELECT 
                        REFERENCED_TABLE_NAME, 
                        REFERENCED_COLUMN_NAME 
                        FROM INFORMATION_SCHEMA.`KEY_COLUMN_USAGE`
                        WHERE REFERENCED_TABLE_NAME is not null
                        and REFERENCED_COLUMN_NAME is not null
                        and TABLE_SCHEMA='".$this->getConfig()->getConfigParam('dbName')."'
                        and lower(table_name)=lower('$table')
                        and lower(COLUMN_NAME)=lower('$field')";
                        $aRow = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getRow($sSql);
                        //$aRow = $this->getConfig()->getRow($sSql, false);
                        $item['foreign'] = true;
                        $item['foreign_table'] = $aRow['REFERENCED_TABLE_NAME'];
                        $item['foreign_column'] = $aRow['REFERENCED_COLUMN_NAME'];
                        //echo $sSql."<br>";
                    }
                    else
                    {
                        if (substr($field, 0, 2) == "ox" && strtolower(substr($field, strlen($field) - 2)) == "id") {
                            //oxshopid

                            //search for oxshop
                            $t = substr($field, 0, strlen($field) - 2);
                            $sql = "SELECT count(*)
                            FROM INFORMATION_SCHEMA.TABLES 
                            where table_schema='".$this->getConfig()->getConfigParam('dbName')."'
                            and table_type='BASE TABLE'
                            and lower(table_name)=lower('$t')";
                            if (oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne($sql) == "1") {
                                $item['foreign'] = true;
                                $item['foreign_table'] = $t;
                                $item['foreign_column'] = "oxid";
                            }

                            //search for oxshops
                            $t.="s";
                            $sql = "SELECT count(*)
                            FROM INFORMATION_SCHEMA.TABLES 
                            where table_schema='".$this->getConfig()->getConfigParam('dbName')."'
                            and table_type='BASE TABLE'
                            and lower(table_name)=lower('$t')";
                            if (oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne($sql) == "1") {
                                $item['foreign'] = true;
                                $item['foreign_table'] = $t;
                                $item['foreign_column'] = "oxid";
                            }
                        }
                        /*
                        if (substr($field, 0, 2) == "f_") {
                            $t = substr($field, 2);
                            $sql = "SELECT count(*)
                            FROM INFORMATION_SCHEMA.TABLES 
                            where table_schema='".$this->getConfig()->getConfigParam('dbName')."'
                            and table_type='BASE TABLE'
                            and lower(table_name)=lower('$t')";
                            if (oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne($sql) == "1") {
                                $item['foreign'] = true;
                                $item['foreign_table'] = $t;

                                $sql = "SELECT count(*) 
                                FROM INFORMATION_SCHEMA.COLUMNS 
                                where lower(table_name)=lower('".$t."') 
                                and lower(column_name)='cpid'
                                and table_schema='".$this->getConfig()->getConfigParam('dbName')
                                    ."'";
                                //echo $sql;
                                $r = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne($sql);
                                if ($r == "1") {
                                    $item['foreign_column'] = "cpid";
                                } else {
                                    //echo $sql."<br>";
                                    //echo $r."<br>";
                                    $item['foreign_column'] = "index1";
                                }
                            }
                        } elseif (substr($field, 0, 1) == "f") {
                            $t = substr($field, 1);
                            $sql = "SELECT count(*)
                            FROM INFORMATION_SCHEMA.TABLES 
                            where table_schema='".$this->getConfig()->getConfigParam('dbName')."'
                            and table_type='BASE TABLE'
                            and lower(table_name)=lower('$t')";
                            if (oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne($sql) == "1") {
                                $item['foreign'] = true;
                                $item['foreign_table'] = $t;
                                $item['foreign_column'] = "index1";
                            }
                        }
                        elseif (strtolower(substr($field, strlen($field) - 2)) == "id")
                        {
                            $t = substr($field, 0, strlen($field) - 2);
                            $sql = "SELECT count(*)
                            FROM INFORMATION_SCHEMA.TABLES 
                            where table_schema='".$this->getConfig()->getConfigParam('dbName')."'
                            and table_type='BASE TABLE'
                            and lower(table_name)=lower('$t')";
                            if (oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne($sql) == "1") {
                                $item['foreign'] = true;
                                $item['foreign_table'] = $t;
                                $item['foreign_column'] = "oxid";
                            }
                        }
                        */

                    }


                    //if($item['primary']==false && $item['foreign']==false)
                    if($item['foreign']==false)
                    {
                        $relations = $this->_relations;
                        foreach($relations as $relation)
                        {
                            /*
                            if($relation['from_table']==$table && $relation['from_column']==$field)
                            {
                                $item['foreign']=true;
                                $item['foreign_table'] =$relation['to_table'];
                                $item['foreign_column']=$relation['to_column'];
                                break;
                            }
                            */
                            if($relation['to_table']==$table && $relation['to_column']==$field)
                            {
                                $item['foreign']=true;
                                $item['foreign_table'] =$relation['from_table'];
                                $item['foreign_column']=$relation['from_column'];
                                if(isset($relation['relation']))
                                    $item['relation'] = $relation['relation'];
                                break;
                            }

                        }
                    }


                    $list[]=$item;
                }
                $rs->fetchRow();
            }
        }
        return $list;
    }
}