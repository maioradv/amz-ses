<?php
/**
 * SimpleEmailServiceEnvelope Class Doc Comment.
 *
 * @category Class
 * @package  AmazonSimpleEmailService
 * @author   Okamos <okamoto@okamos.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/okamos/php-ses
 */
class BulkEmailServiceEnvelope
{
    private $_returnpath;

    protected $_source = '';
    protected $_template = '';
    protected $_DefaultTemplateData = '';
    protected $_ReplyToAddresses = '';

    private $_charset = 'UTF-8';

    protected $_destinations = array();

    public $action = 'SendBulkTemplatedEmail';

    /**
     * Set required values, and build instance.
     *
     * @param string $from        The email address that is sending the email.
     * This email address must be either individually verified with Amazon SES.
     * @param string $subject     A short summary of the content.
     * @param string $message     The message body.
     * @param string $htmlMessage The HTML message body. Optional.
     */
    public function __construct($from, $template, $DefaultTemplateData = null, $ReplyToAddresses = null)
    {
        $this->_source = $from;
        $this->_template = $template;
        $this->_DefaultTemplateData = $DefaultTemplateData;
        $this->_ReplyToAddresses = $ReplyToAddresses;
    }

    /**
     * Add To: to the destination for a email(s).
     *
     * @param string[] $to List of email(s).
     *
     * @return void
     */
    public function addDestinations($dest)
    {
		$this->_destinations = array_merge($this->_destinations, $dest);
    }

    /**
     * Set charset, default UTF-8.
     *
     * @param string $charset The character set of the content.
     *
     * @return void
     */
    public function setCharset($charset)
    {
        $this->_charset = $charset;
    }

    /**
     * Validates instance.
     * This is used before attempting a SendEmail or SendRawEmail.
     *
     * @return Error or boolean
     */
    public function validate()
    {
        if (empty($this->_destinations)) {
            return new SimpleEmailServiceError('Destination');
        }
        if (empty($this->_source)) {
            return new SimpleEmailServiceError('EmailSource');
        }		
        if (empty($this->_template)) {
            return new SimpleEmailServiceError('NoTemplate');
        }		
        if (empty($this->_DefaultTemplateData)) {
            return new SimpleEmailServiceError('DefaultTemplateData');
        }		

        return true;
    }

    /**
     *  Build parameters for sendEmail.
     *
     *  @return object
     */
    public function buildParameters()
    {
        $params = array();
		$this->action = 'SendBulkTemplatedEmail';
		
		$params['Source'] = $this->_source;
		$params['ReplyToAddresses'] = $this->_ReplyToAddresses;
		$params['Template'] = $this->_template;
		$params['DefaultTemplateData'] = $this->_DefaultTemplateData;
		
        $i = 1;
        foreach ($this->_destinations as $key => $value) {
            $params['Destinations.member.'. $i .'.Destination.ToAddresses.member.1'] = $value['Destination']['ToAddresses'][0];
			$params['Destinations.member.'. $i .'.ReplacementTemplateData'] = $value['ReplacementTemplateData'];
            $i++;
        }

        $params['Source'] = $this->_source;

        if ($this->_returnpath) {
            $params['ReturnPath'] = $this->_returnpath;
        }

        return $params;
    }
}
