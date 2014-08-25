<?php
/**
 * Form
 * This class represent SimplCMS HTML form. It contains mechanisms used for rendering and handling
 * common form operations. Note: form validation functionality comes with a plugin called FormValidator.
 * 
 */
namespace spcms\core;

class Form extends BaseClass
{
	const METHOD_POST = 'POST';
	
	const METHOD_GET = 'GET';

	/**
	 * Form error captured by validation
	 * @var type 
	 */
	private $_errors = array();

	/**
    * Unique form name to distinquishe multiple forms
    * @var string 
    */
    private $name;
    
	/**
	 * Form request method
	 * @var string GET|POST
	 */
    private $method;
    
    /**
    * Action where form gets submitted. Default: current url
    * @var string
    */
    private $action;
	
	/**
	 * CSRF token
	 * @var string CSRF token which is generated when form gets initialized.
	 */
    private $csrfToken;
	
    /**
     * Used for passing information between multipage forms.
     * @var array
     */
    private $_storage = array();

	/**
	 * Data submitted by form with POST|GET
	 * @var array
	 */
	private $data = array();
	
	/**
	 * Special internal attributes (prefixed with #) used only by Form.
	 * @var array 
	 */
	private $_internalAttributes = array('#captureValue', '#group');
	
	/**
	 * Submission indicator.
	 * @var boolean
	 */
	private $_isSubmitted = false;
	
	/**
	 * Raw untrusted form data.
	 * @var type 
	 */
	private $_rawData = array();

	/**
	 * Initialize form
	 * @param string $name Unique form name.
	 * @param string $method GET|POST. Default: POST.
	 * @param string $action URL where form gets submitted. Default: current url.
	 * @param array $options Additional form processing options:
	 * - externalAction = false; If this is set to true, external action path can be  used. Use this if you dont want your form to use internal application path
	 */
    public function __construct($name, $method, $action = null, array $options = array()) 
    {		
        $this->name = $name;
		$this->method = $method;
		$this->_isSubmitted = $this->isSubmitted();
		
		// CSRF form protection with token
		$this->csrfToken = $this->generateCsrfToken();		
		
        // If no action is provided, form will be submitted to current action
        if($action !== null) {			
			if(isset($options['externalAction']) && $options['externalAction'] === true)
				$this->action = $action;
			else
				$this->action = \SimplCMS::$app->baseUrl. "/{$action}";
		}
        else
            $this->action = \SimplCMS::$app->request->getRequestUri();
		
		// Run CSRF validation and capture data on submission
		$this->captureFormData();
		
		// Save CSRF token for later request validation
		\SimplCMS::$app->session->set("formToken_{$this->name}", $this->csrfToken);		
    }

	/**
    * Create form start tag and set additional form attributes.
    * @param array $attributes
    * @return string 
    */
    public function start(array $attributes = array()) 
    {
        $output  = "<form method='{$this->method}' action='{$this->action}' {$this->buildAttributes($attributes)}>\r";
		$output  .= $this->elementInput('formId', array('type' => 'hidden', 'value' => $this->csrfToken), false);
		return $output;
    }
    
    /**
     * Create element attributes
     * @param array $attributes
     * @return string
     */
    private function buildAttributes(array $attributes)
    {
        $output = '';
        
        if(sizeof($attributes) > 0)
        {
            foreach ($attributes as $name => $value)
            {
				// Skip internal attributes from being rendered
				if (in_array($name, $this->_internalAttributes))
					continue;
				
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
	

	public function end()
    {
        return "</form>\r";
    }
    
    /**
     * Create input form element
     * @param string $elementName
     * @param array $attr Input element attributes.
	 * Special attributes:
	 * - captureValue (boolean): Maintain value after form submission. Default: true.
     * @param boolean Render element or send it as a value
     */
    public function elementInput($elementName, array $attributes = array(), $render = true)
    {
        if(!isset($attributes['type']))
            throw new \Exception("Input element '{$elementName}' is missing 'type' attribue!");
		
		// Set special internal attributes
		$attributes['#captureValue'] = isset($attributes['#captureValue']) ? (boolean) $attributes['#captureValue'] : true;
		$attributes['#group'] = isset($attributes['#group']) ? (boolean) $attributes['#group'] : false;
		
		// Maintain value after form is being submitted
		if ($attributes['#captureValue'] === true && array_key_exists($elementName, $this->_rawData))
			$attributes['value'] = $this->_rawData[$elementName];
		
		// Return element as group
		if ($attributes['#group'] === true)
			return "<input name='{$this->name}[{$elementName}][]' {$this->buildAttributes($attributes)} /> \r";
			
        return "<input name='{$this->name}[{$elementName}]' {$this->buildAttributes($attributes)} /> \r";
    }
    
    /**
     * Create input-text element
     * @param array $attributes
     * @return string 
     */
    public function elementText($elementName, array $attributes = array())
    {
        $attributes['type'] = 'text';
        
        return $this->elementInput($elementName, $attributes);
    }
	
	/**
	 * Generate random CSRF token
	 * @return string CSRF token
	 */
	private function generateCsrfToken() 
	{
		return md5(uniqid(microtime(), true));
	}

	/**
     * Create input-password element
     * @param array $attributes
     * @return string 
     */
    public function elementPassword($elementName, array $attributes = array())
    {
        $attributes['type'] = 'password';
        
        return $this->elementInput($elementName, $attributes);
    }	
    
    /**
     * Create input-submit element
     * @param array $attributes
     * @return string 
     */
    public function elementSubmit($elementName, array $attributes = array())
    {
        $attributes['type'] = 'submit';
        
        return $this->elementInput($elementName, $attributes);
    }
	
	/**
	 * Create checbox element
	 * @param string $elementName
	 * @param array $attributes
	 * Special attributes:
	 * - captureValue (boolean): Maintain value after form submission. Default: true.
	 * - group (boolean): If you plan to group multiple checkboxes, set this to true. Group means that
	 * a set of checkboxes have the same name but different values. You also have to pass 'value' attribute as array('value' => 'name'...). Default: false.
	 * @return string
	 */
	public function elementCheckbox($elementName, array $attributes = array())
	{
		$element = '';
		$attributes['type'] = 'checkbox';	
		// No need to capture value for checkboxes
		$attributes['#captureValue'] = false;
		
		// Create hidden element to avoid checking if checkbox was set,
		// and make sure checkbox is alway present even if not checked.
		$element .= $this->elementInput($elementName, array(
			'type' => 'hidden', 
			'value' => '',
			// Make sure it is always empty
			'#captureValue' => false
		));		
		
		// Build group of checkboxes when 'value' is array
		if (isset($attributes['#group']) && $attributes['#group'] === true && is_array($attributes['value']))
		{			
			foreach ($attributes['value'] as $value => $name)
			{
				// Set element as checked if selected
				if (isset($this->_rawData[$elementName]) && in_array($value, (array) $this->_rawData[$elementName]) === true)
					$attributes['checked'] = 'checked';
				else if (isset($attributes['checked']))
					unset($attributes['checked']);
						
				// Append group element
				$attributes['value'] = $value;
				$element .= '<span>'. $this->elementInput($elementName, $attributes). "{$name}</span>";
			}
		}
		else 
		{
			// We are working with a single element checkbox
			// Set as 'checked' when selected
			if(isset($this->_rawData[$elementName]) && !empty($this->_rawData[$elementName]))
				$attributes['checked'] = 'checked';			
			
			$element .= $this->elementInput($elementName, $attributes);
		}
		
		return $element;
	}

	/**
     * Create <select> element
     * @param string $elementName
     * @param array $options
     * @param array $attributes Special attributes: selected => 'value' to tell which option is selected by default.
     * @return string
     */
    public function elementSelect($elementName, array $options = array(), array $attributes = array())
    {
        // Handle single or multiple selection list
        if(!isset($attributes['multiple']))
            $element = "<select name='{$this->name}[{$elementName}]' {$this->buildAttributes($attributes)}>\r";
        else
            $element = "<select name='{$this->name}[{$elementName}][]' {$this->buildAttributes($attributes)}>\r";
            
        // If no default option is selected, create an empty option
        // to get select list submitted as empty.
        if(!isset($attributes['selected']))
            $element .= '<option selected value=""></option>';
  
        // Append option values to select menu
        if(sizeof($options) > 0)
        {
            foreach ($options as $value => $name)
            {
                // Check for default selected option
                if(isset($attributes['selected']) && $attributes['selected'] === $value)
                    $element .= "<option selected='selected' value='{$value}'>{$name}</option>\r";
                else
                    $element .= "<option value='{$value}'>{$name}</option>\r";
            }
        }
        
        $element .= "</select>\r";
        
        return $element;
    }
    
    /**
     * Create <textarea> element
     * @param type $elementName
     * @param array $attributes
     * @param string $value Default element value
     * @return string 
     */
    public function elementTextarea($elementName, array $attributes = array(), $value = null)
    {
		$attributes['#captureValue'] = isset($attributes['#captureValue']) ? (boolean) $attributes['#captureValue'] : true;		
				
		// Maintain value after form is being submitted
		if ($attributes['#captureValue'] === true && array_key_exists($elementName, $this->_rawData))
			$value = $this->_rawData[$elementName];
		else if ($value !== null)
			$value = $value;

        return "<textarea name='{$this->name}[{$elementName}]' {$this->buildAttributes($attributes)}>{$value}</textarea>";
    }

    /**
    * Get data submitted by the form. This method automatically handles security checking (CSRF, method type, etc..).
	* Note: never use superglobals to get data, this method should always be your first entering point for getting form submitted data.
	* No data will be returned if validation fails.
	*
	* @implementedHooks 
	* - processData: process data before returned
	*	params: (array) &$data
    * @return array
    */
    public function getData()
    {
		if ($this->isValid() === true)
		{
			// processData hook implementation
			$this->notify('processData', $this->data);		
			return $this->data;
		}
		return array();
    }
	
	/**
	 * Get raw submitted data by form. This data should NOT BE TRUSTED!
	 * @param string $elementName Optional element name.
	 * @return mixed array|string|null
	 */
	public function getRawData($elementName = '')
	{
		if ($this->_isSubmitted === true) 
		{
			switch($this->method)
			{
				case self::METHOD_POST:	
					
					if (isset($elementName) && isset($this->_rawData[$elementName]))
						return $this->_rawData[$elementName];

					return $this->_rawData;				
				break;
				case self::METHOD_GET:
					
					if (isset($elementName) && isset($this->_rawData[$elementName]))
						return $this->_rawData[$elementName];

					return $this->_rawData;
				break;
			}
		}
		return null;
	}

	/** 
	* Capture form submitted data to be avaliable by getData()
	* @implementedHooks 
	* - beforeSubmit: process data before submission.
	*	params: (array) &$data
	*/
	private function captureFormData()
	{
		// Handle different method types
        if($this->_isSubmitted === true)
        {	
			switch($this->method)
			{
				case self::METHOD_POST:
					
					// Capture raw untrusted data
					$this->_rawData = $_POST[$this->name];
					
					// Capture CSRF safe data 
					if ($this->csrfTokenValidation() === true) 
					{
						$this->data = $_POST[$this->name];
						// beforeSubmit hook
						$this->notify('beforeSubmit', $this->data);
					}
					
				break;
				case self::METHOD_GET:
					
					// Capture raw untrusted data
					$this->_rawData = $_GET[$this->name];
					
					// Capture CSRF safe data 
					if ($this->csrfTokenValidation() === true)
					{
						$this->data = $_GET[$this->name];					
						// beforeSubmit hook
						$this->notify('beforeSubmit', $this->data);
					}
					
				break;
			}
        }
	}

	/**
    * Check if form was submitted.
    * @return boolean 
    */
    public function isSubmitted()
    {
        switch($this->method)
        {
            case self::METHOD_POST:
                return isset($_POST[$this->name]) ? true : false;
            break;
            case self::METHOD_GET:
                return isset($_GET[$this->name]) ? true : false;
            break;
        }
        return false;
    }
	
	/**
	 * Check if form is without errors
	 */
	public function isValid()
	{
		return empty($this->_errors) ? true : false;
	}

	/**
	 * CSRF token validation. Validate session token against submitted form token.
	 * @return boolean
	 */
	private function csrfTokenValidation()
	{
		if (!isset($this->_rawData['formId']))
			return false;
		
		// Check if submitted token matches the on stored in session
		if($this->_rawData['formId'] === \SimplCMS::$app->session->get("formToken_{$this->name}"))
			return true;
		
		return false;
	}
	
	
	public function setError($type, $message)
	{
		$this->_errors[$type] = $message;
	}
	
	/**
	 * Get form errors..
	 * @return array
	 */
	public function getErrors()
	{
		return $this->_errors;
	}
	
	/**
	 * Get unique form name.
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
}