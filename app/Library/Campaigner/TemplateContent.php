<?php
namespace App\Library\Campaigner;
class TemplateContent
{

    /**
     * @var string $HTML
     */
    protected $HTML = null;

    /**
     * @var string $Text
     */
    protected $Text = null;

    /**
     * @param string $HTML
     * @param string $Text
     */
    public function __construct($HTML, $Text)
    {
      $this->HTML = $HTML;
      $this->Text = $Text;
    }

    /**
     * @return string
     */
    public function getHTML()
    {
      return $this->HTML;
    }

    /**
     * @param string $HTML
     * @return TemplateContent
     */
    public function setHTML($HTML)
    {
      $this->HTML = $HTML;
      return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
      return $this->Text;
    }

    /**
     * @param string $Text
     * @return TemplateContent
     */
    public function setText($Text)
    {
      $this->Text = $Text;
      return $this;
    }

}
