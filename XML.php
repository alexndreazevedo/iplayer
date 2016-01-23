<?php
class XML
{
    
    public static function toArray($target = null, $params = null, $encoding = 'UTF-8') {
        
        if (!$params) {
            
            return array();
            
        }

        if (!function_exists('xml_parser_create')) {

            return array();
            
        }

        $xml = xml_parser_create($encoding);
        
        xml_parser_set_option($xml, XML_OPTION_TARGET_ENCODING, $encoding);
        xml_parser_set_option($xml, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($xml, trim($params), $values);
        xml_parser_free($xml);

        if (!$values){
            
            return;
            
        }

        $array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();

        $current = &$array;

        $repeated_tag_index = array();
        
        foreach ($values as $data) {
            
            unset($attributes, $value);

            extract($data);

            $result = array();
            $attributes_data = array();
            
            if (isset($value)) {
                
                $result = html_entity_decode(htmlentities($value, ENT_COMPAT, $encoding), ENT_COMPAT,'ISO-8859-1');

            }

            if (isset($attributes)) {
                
                foreach ($attributes as $attr => $val) {
                    
                    $attributes_data[$attr] = html_entity_decode(htmlentities($val, ENT_COMPAT, $encoding), ENT_COMPAT,'ISO-8859-1');
                    
                }
                
            }

            if ($type == 'open') {

                if($tag == 'item'){

                    $tag = $attributes_data['id'];

                }
                
                $parent[$level - 1] = &$current;
                
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) {

                    $current[$tag] = $result;
                    
                    $repeated_tag_index[$tag . '_' . $level] = 1;

                    $current = &$current[$tag];
                    
                } else {
                    
                    if (isset($current[$tag][0])) {
                        
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                        
                    } else {
                        
                        $current[$tag] = array($current[$tag], $result);
                        $repeated_tag_index[$tag . '_' . $level] = 2;

                    }
                    
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = &$current[$tag][$last_item_index];
                    
                }
                
            } elseif ($type == 'complete') {
                
                if (!isset($current[$tag])) {
                    
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    
                } else {
                    
                    if (isset($current[$tag][0]) and is_array($current[$tag])) {
                        

                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                        $repeated_tag_index[$tag . '_' . $level]++;
                        
                    } else {
                        
                        $current[$tag] = array($current[$tag], $result);
                        
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        
                        if ($get) {
                            
                            if (isset($current[$tag . '_attr'])) {
                                
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                
                                unset($current[$tag . '_attr']);
                                
                            }

                            if ($attributes_data) {
                                
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                                
                            }
                            
                        }
                        
                        $repeated_tag_index[$tag . '_' . $level]++;
                        
                    }
                    
                }
                
            } elseif ($type == 'close') {
                
                $current = &$parent[$level - 1];
                
            }
            
        }

        if($target != null){
            
            if(isset($array[$target])){
        
                return $array[$target];
                
            }
            
        }
        
        return $array;
        
    }

}

$array = array(

    array(

        'idpontocampanha'     => '323',
        'idponto'             => '17',
        'idcampanha'          => '103',
        'unlink'              => '0',
        'dthistorico'         => '2011-11-22 15:35:22',
        'listid'              => '1',
        'idcliente'           => '7',
        'idcampanhacategoria' => '2',
        'idusuario'           => '37',
        'idanunciante'        => '0',
        'nome'                => 'Marista terra saude',
        'duracao'             => '10',
        'dtentrada'           => '2011-11-22',
        'dtsaida'             => '2012-12-30',
        'diasemana'           => 'TODOS',
        'hrentrada'           => '15:40:00',
        'hrsaida'             => '15:35:00',
        'campanhacategoria'   => 'conteudo',
        'midias'              => array(

            array(

                'nome'            => 'Terra - Saúde',
                'content'         => 'http://sistema.trupepromocao.com.br/public/xml/saude.xml',
                'ext'             => 'xml',
                'tipo'            => 'biblioteca',

            )

        ),

        array(

            'idpontocampanha'     => '325',
            'idponto'             => '17',
            'idcampanha'          => '105',
            'unlink'              => '0',
            'dthistorico'         => '2011-11-22 15:38:09',
            'listid'              => '4',
            'idcliente'           => '7',
            'idcampanhacategoria' => '2',
            'idusuario'           => '37',
            'idanunciante'        => '0',
            'nome'                => 'Marista terra esporte',
            'duracao'             => '10',
            'dtentrada'           => '2011-11-22',
            'dtsaida'             => '2012-12-30',
            'diasemana'           => 'TODOS',
            'hrentrada'           => '15:44:00',
            'hrsaida'             => '15:38:00',
            'campanhacategoria'   => 'conteudo',
            'midias'              => array(

                array(

                    'nome'            => 'Terra - Esportes',
                    'content'         => 'http://sistema.trupepromocao.com.br/public/xml/esportes.xml',
                    'ext'             => 'xml',
                    'tipo'            => 'biblioteca',

                )

            )

        )

    )

);

$xml = file_get_contents('config/playlist.xml');

var_dump(XML::toArray('playlist', $xml));