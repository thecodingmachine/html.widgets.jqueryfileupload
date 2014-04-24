<?php
namespace Mouf\Html\Widgets\JqueryFileUpload;

use Mouf\Html\Widgets\Form\Styles\StylableFormField;

use Mouf\Html\Tags\Label;
use Mouf\Html\Tags\Input;
use Mouf\Html\Renderer\Renderable;
use Mouf\Utils\Value\ValueInterface;
use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\Utils\Value\ValueUtils;
use Mouf\Html\HtmlElement\HtmlString;

/**
 * A JqueryFileUploadField represent a couple of &lt;label&gt; and JqueryFileUploadWidget fields.
 * This class is "renderable" so you can overload the way label and input fields are displayed.
 * 
 * @author David Négrier <d.negrier@thecodingmachine.com>
 */
class JqueryFileUploadField implements HtmlElementInterface {
	use Renderable {
		Renderable::toHtml as toHtmlParent;
	}
	
	use StylableFormField;
	
	/**
	 * @var Label
	 */
	protected $label;

	/**
	 * @var JqueryFileUploadWidget
	 */
	protected $jqueryFileUploadWidget;
	
	/**
	 * Boolean, true if the field is required, else false
	 * @var bool
	 */
	protected $required = false;
	
	/**
	 * @var HtmlElementInterface
	 */
	protected $helpText;
	
	private static $LASTID = 0;

	/**
	 * Constructs the textfield.
	 * 
	 * @param string|ValueInterface $label
	 * @param string|ValueInterface $name
	 */
	public function __construct($label = null, $name = null) {
		$this->label = new Label();
		$this->jqueryFileUploadWidget = new JqueryFileUploadWidget();
		if ($label !== null) {
			$this->label->addText($label);
		}
		if ($name !== null) {
			$this->jqueryFileUploadWidget->setName($name);
		}
	}
	
	/**
	 * Specifies one or more classnames for the label
	 *
	 * @param array|ValueInterface $classes
	 * @return static
	 */
	public function setLabelClasses(array $classes) {
		$this->label->setClasses($classes);
		return $this;
	}
	
	/**
	 * Adds a CSS class to the label
	 *
	 * @param string|ValueInterface $class
	 * @return static
	 */
	public function addLabelClass($class) {
		$this->label->addClass($class);
	}
	
	/**
	 * Completely replaces the label element with the label you provide.
	 * Use this function only if you want to perform very specific tasks you cannot do with the methods provided above.
	 *
	 * @param Label $label
	 * @return static
	 */
	public function setLabel(Label $label) {
		$this->label = $label;
		return $this;
	}

	/**
	 * Returns the label object for this field (as an object implementing the Label class)
	 *
	 * @return Label
	 */
	public function getLabel() {
		return $this->label;
	}
	
	/**
	 * Completely replaces the JqueryFileUploadWidget element with the input you provide.
	 * Use this function only if you want to perform very specific tasks you cannot do with the methods provided above.
	 *
	 * @param JqueryFileUploadWidget $jqueryFileUploadWidget
	 * @return static
	 */
	public function setJqueryFileUploadWidget(JqueryFileUploadWidget $jqueryFileUploadWidget) {
		$this->jqueryFileUploadWidget = $jqueryFileUploadWidget;
		return $this;
	}

	/**
	 * Returns the JqueryFileUploadWidget object for this field (as an object implementing the JqueryFileUploadWidget class)
	 *
	 * @return JqueryFileUploadWidget
	 */
	public function getJqueryFileUploadWidget() {
		return $this->jqueryFileUploadWidget;
	}
	
	/**
	 * Set whether the field is required or not
	 *
	 * @param bool $required
	 * @return static
	 */
	public function setRequired($required) {
		$this->required = $required;
		return $this;
	}
	
	/**
	 * Return whether the field is required or not
	 *
	 * @return bool
	 */
	public function isRequired() {
		return $this->required;
	}
	
	/**
	 * Sets the help text for this text field.
	 * 
	 * @param string|ValueInterface|HtmlElementInterface $helpText
	 */
	public function setHelpText($helpText) {
		if ($helpText instanceof ValueInterface) {
			$helpText = ValueUtils::val($helpText);
		}
		if(empty($helpText)) {
			$this->helpText = null;
		}
		elseif ($helpText instanceof HtmlElementInterface) {
			$this->helpText = $helpText;
		} else {
			$this->helpText = new HtmlString((string) $helpText);
		}
	}
	
	/**
	 * Returns the help text for this field (as an object implementing the  HtmlElementInterface)
	 * 
	 * @return HtmlElementInterface
	 */
	public function getHelpText() {
		return $this->helpText;
	}
	
	/**
	 * Renders the object in HTML.
	 * The Html is echoed directly into the output.
	 */
	public function toHtml() {
		
// 		if ($this->label->getFor() === null) {
// 			$id = $this->input->getId();
// 			if ($id === null) {
// 				$id = "mouf_html_widgets_".$this->input->getType()."field_".self::$LASTID;
// 				$this->jqueryFileUploadWidget->setI
// 				$this->label->setFor($id);
// 				self::$LASTID++;
// 			}
// 			else {
// 				$this->label->setFor($this->input->getId());
// 			}
// 		}
		
		$this->toHtmlParent();
	}
}