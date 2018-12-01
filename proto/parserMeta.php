<?php
/**
 * Created by PhpStorm.
 * User: liugaoyun
 * Date: 2018/11/30
 * Time: 下午5:36
 */
$filename = $argv[1];
if(!file_exists($filename)){
    exit("{$filename} not exist");
}

if(!is_file($filename)){
    exit("{$filename} not file");
}

$fh = fopen($filename, 'r');

if(!$fh){
    exit("{$filename} cannot open");
}

//protobuf内置数据类型
$interval_types = ['double','float','int32','int64','uint32','uint64','sint32','sint64','fixed32','fixed64','sfixed32','sfixed64','bool','string','bytes'];


$result = [];
$message_name = []; //当前message
$fields = [];
while (($row = fgets($fh, 4096)) !== false) {
    if(strpos($row, "package") !== false){//提取包名
        $pattern = '/package\s+(?<package>[a-zA-Z]+).*/';
        preg_match($pattern, $row, $package);
        $result['package'] = $package['package'];
    }

    if(strpos($row, "message") !== false){   //开始一段message提取
        $pattern = '/message\s+(?<Name>[a-zA-Z]+)\s*/';
        preg_match($pattern, $row, $name);
        $message_name = $name['Name'];
    }

    if(strpos($row, "=") !== false && strpos($row, "syntax") === false){   //属性行
        if(strpos($row, "repeated") !== false){
            //        echo $row . "\n";
            $pattern = '/repeated\s+(?<FieldType>\w+)\s+(?<FieldName>\w+)\s*=/';
            preg_match($pattern, $row, $field);
            $tmpField = [
                'field_type'=>$field['FieldType'],
                'field_name'=>$field['FieldName'],
                'is_array'=>'true',
                'is_map'=>'false',
                'map_key_type'=>'null'
            ];
        }elseif (strpos($row, "map") !== false){
            $pattern = '/map<(?<KeyType>[a-zA-Z]+),\s*(?<ValueType>[a-zA-Z]+)>\s+(?<FieldName>\w+)\s*=/';
            preg_match($pattern, $row, $map);
            $tmpField['field_type'] = $map['ValueType'];
            $tmpField['field_name'] = $map['FieldName'];
            $tmpField['is_array'] = 'false';
            $tmpField['is_map'] = 'true';
            $tmpField['map_key_type'] = $map['KeyType'];
        }else{
            $pattern = '/\s+(?<FieldType>\w+)\s+(?<FieldName>\w+)\s*=/';
            preg_match($pattern, $row, $field);
            $tmpField = [
                'field_type'=>$field['FieldType'],
                'field_name'=>$field['FieldName'],
                'is_array'=>'false',
                'is_map'=>'false',
                'map_key_type'=>'null'
            ];
        }




        //是否是自定义类型判断
        if(in_array($tmpField['field_type'], $interval_types)){
            $tmpField['is_scalar'] = 'true';
        }else{
            $tmpField['is_scalar'] = 'false';
            $tmpField['field_type'] = $result['package'] . '\\' . $tmpField['field_type'];
        }

        $fields[] = $tmpField;
    }

    if(strpos($row, "}") !== false){   //消息结束行
        $result['messages'][] = [
            'name'=>$message_name,
            'fields'=>$fields
        ];
        $message_name = $fields = null;
    }
}

if (!feof($fh)) {
    echo "Error: unexpected fgets() fail\n";
}
fclose($fh);

$output = '<?php' .PHP_EOL. PHP_EOL. 'return [' . PHP_EOL;
foreach ($result['messages'] as $m){
    $property = '[';
    if(!empty($m['fields'])){
        foreach ($m['fields'] as $v){
            $subitem = <<<EOF
        '{$v['field_name']}'=>[
            'IsScalarType'=>{$v['is_scalar']},
            'FieldType'=>'{$v['field_type']}', 
            'IsArray'=>{$v['is_array']}, 
            'IsMap'=>{$v['is_map']}, 
            'MapKeyType'=>'{$v['map_key_type']}'
        ],

EOF;

            $property .= PHP_EOL. $subitem;
        }
    }

    $property .= '    ]';
    $item = <<<EOF
    '{$result['package']}\\{$m['name']}'=>$property,
EOF;


    $output .= $item . PHP_EOL;
}

$output .= '];';


file_put_contents("./msg.proto.php", $output);