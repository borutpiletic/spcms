<?php
namespace spcms\core;
use \SimplCMS;

/**
 * Simple page model
 * When new page is created it can be save with PageWriter component
 */
class Page
{    
	private $id;

	protected $attributes;
	
	public $title;
	
	public $body;
	
	public $url;
	
	public $metaKeywords;
	
	public $metaDescription;

	public $requiredAttr = array(
        'body', 'title', 'url', 'metaKeywords', 'metaDescription'
    );
    
    protected $invalidAttributes;
    
    protected $errors = array();
    
    /**
     * @param array $attributes Set model attributes same as setAttributes
     */
    public function __construct($attributes = array())
    {
        if(!empty($attributes) && is_array($attributes))
            $this->setAttributes($attributes);
    }

    /**
     * Populate page object with attribute values and perform 
     * value validation and sanitation.
     * @param array $attributes
     * @return boolean
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        
        foreach ($this->attributes as $attr => $value)
        {
            if(property_exists($this, $attr))
                $this->{$attr} = $value;
        }
    }
    
    /**
     * Sanitize attribute value
     * @param string $name
     * @param string $value
     * @return string
     */
    protected function attrSanitize($name, $value)
    {
        $value = trim($value);
        
        switch($name)
        {
            case 'title':
            case 'body':
                $value = htmlentities($value, ENT_QUOTES, \SimplCMS::app()->getConfig()->charset );
            break;
        }
        
        return $value;
    }
    
    /**
     * Return model attributes
     * @return type
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * Check if page attribute exsists
     * @param string $attributeName
     * @return boolean
     */
    public function hasAttribute($attributeName)
    {
        return isset($this->attributes[$attributeName]) ? true : false;
    }
    
    /**
     * Return page attribute value
     * @return mixed Attribute value or throw exception if no attribute has been found
     */
    public function getAttribute($attributeName)
    {
        if(!$this->hasAttribute($attributeName))
            throw new \Exception("Page model has no attribute named: {$attributeName}");
            
        return $this->attributes[$attributeName];
    }
	
	public function getId()
	{
		return $this->id;
	}
}
