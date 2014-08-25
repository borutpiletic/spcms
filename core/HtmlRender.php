<?php
namespace spcms\core;

/*
 * Helper class for rendering common HTML elements
 * 
 */
class HtmlRender
{
    /**
     * Render HTML list based on array structure
     * @param array $elements
     * @param boolean $recursive
     */
    public static function htmlList(array $elements, $recursive = false)
    {
        static $level = 0;
        
        $output = "<ul class='level_{$level}'>";
        
        foreach ($elements as $element)
        {
            if(is_array($element) && !empty($element))
            {
                $level++;
                $output .= self::htmlList($element, $recursive);
            }
            else
                $output .= "<li class='level_{$level}'>{$element}</li>";
        }
        return $output .= '</ul>';
    }


	/**
     * Render 
     * @param array $data
     * @param array $columns
     * @return type
     */
    public static function table(array $data,array $columns)
    {
        $output = '<table>';
        
        // Render table header
        $output .= '<thead>';
        foreach ($columns as $column)
        {
            $output .= "<th>{$column}</th>";
        }
        $output .= '</thead>';
        
        // Render table body
        $output .= '<tbody>';
        foreach ($data as $row)
        {
            $output .= '<tr>';
            
            //print_r($row); exit;
            
            foreach ($row as $columnName => $columnValue)
                if(in_array($columnName, $columns) || array_key_exists($columnName, $columns))
                    $output .= "<td>{$columnValue}</td>";
            
            $output .= '</tr>';
        }
        $output .= '</tbody>';
        
        return $output .= '</table>';
    }
	
    /**
     * Helper method: Create element attributes
     * @param array $attributes
     * @return string
     */
    public static function buildAttributes(array $attributes)
    {
        $output = '';
        
        if(sizeof($attributes) > 0)
        {
            foreach ($attributes as $name => $value)
            {
                $output .= "{$name}=";

                // Handle multiple attribute values
                if(is_array($value))
                {
                $output .= "'";
                $output .= implode(' ', $value);
                $output .= "'";
                }
                else 
                    $output .= "'{$value}'";

                $output .= ' ';
            }
        }
        
        return $output;
    }
}