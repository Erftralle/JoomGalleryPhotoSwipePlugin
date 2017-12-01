<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * JoomGallery PhotoSwipe Plugin 'Integrate PhotoSwipe (4.0+)'
 *
 * With this plugin JoomGallery is able to use the PhotoSwipe Javascript
 * (http://www.photoswipe.com/) for displaying images.
 *
 * By:        Erftralle, based on JoomPhotoswipe by JoomGallery::ProjectTeam
 * Copyright: (c) 2015 Erftralle, (c) 2013 - 2014 JoomGallery::ProjectTeam
 * License:   This software is released under the GNU/GPL License
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY, without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
 */

require_once JPATH_ADMINISTRATOR.'/components/com_joomgallery/helpers/openimageplugin.php';

class plgJoomGalleryPhotoSwipe extends JoomOpenImagePlugin
{
  /**
   * Name of this popup box
   *
   * @var   string
   */
  protected $title = 'PhotoSwipe-v4plus';

  /**
   * Joomgallery configuration
   *
   * @var     object
   */
  private $_jg_config = null;

  /**
   * Joomgallery ambit
   *
   * @var     object
   */
  private $_jg_ambit = null;

  /**
   * Joomla document
   *
   * @var     object
   */
  private $_doc = null;

  /**
   * Flag, whether the browser is a mobile one
   *
   * @var     boolean
   */
  private $_isMobile = null;

  /**
   * Flag, whether the download of images is allowed
   *
   * @var     boolean
   */
  private $_downloadAllowed = null;

  /**
   * Initializes the box by adding all necessary image group independent JavaScript and CSS files.
   * This is done only once per page load.
   *
   * @return  void
   */
  protected function init()
  {
    $this->loadLanguage();

    $this->_doc       = JFactory::getDocument();
    $this->_jg_ambit  = JoomAmbit::getInstance();

    // Load jQuery in 'noConflict' mode
    JHtml::_('jquery.framework');

    // Add PhotoSwipe style sheets
    $this->_doc->addStyleSheet(JURI::root().'media/plg_joomgallery_photoswipe/photoswipe.css');
    $this->_doc->addStyleSheet(JURI::root().'media/plg_joomgallery_photoswipe/default-skin/default-skin.css');

    // Add PhotoSwipe javascript
    $this->_doc->addScript(JURI::root().'media/plg_joomgallery_photoswipe/photoswipe'.(JFactory::getConfig()->get('debug') ? '' : '.min').'.js');
    $this->_doc->addScript(JURI::root().'media/plg_joomgallery_photoswipe/photoswipe-ui-default'.(JFactory::getConfig()->get('debug') ? '' : '.min').'.js');

    // Add plugin's javascript
    $this->_doc->addScript(JURI::root().'media/plg_joomgallery_photoswipe/jg-photoswipe.js');

    // Add PhotoSwipe options
    // $preventSlideshow                  = $this->params->get('cfg_operationmode') ? 'false' : 'true';
    $loop                              = $this->params->get('cfg_loop') ? 'true' : 'false';
    // $autoStartSlideshow                = $this->params->get('cfg_slideautostart') ? 'true' : 'false';
    $captionEl                         = $this->params->get('cfg_captionshow') ? 'true' : 'false';
    // $cfg_captionandtoolbarflipposition = $this->params->get('cfg_captionandtoolbarflipposition') ? 'true' : 'false';
    $shareEl                           = $this->params->get('cfg_shareshow') ? 'true' : 'false';

//     $options  = "                                        {\n";
//     $options .= "                                          preventSlideshow: ".$preventSlideshow.",\n";
//     $options .= "                                          imageScaleMethod: '".$this->params->get('cfg_imagescalemethod')."',\n";
//     $options .= "                                          slideSpeed: ".$this->params->get('cfg_slidespeed').",\n";
//     $options .= "                                          nextPreviousSlideSpeed: ".$this->params->get('cfg_slidespeed').",\n";
//     $options .= "                                          fadeInSpeed: ".$this->params->get('cfg_fadespeed').",\n";
//     $options .= "                                          fadeOutSpeed: ".$this->params->get('cfg_fadespeed').",\n";
//     $options .= "                                          zIndex: ".$this->params->get('cfg_zindex').",\n";
//     $options .= "                                          captionAndToolbarAutoHideDelay: ".$this->params->get('cfg_captionandtoolbarautohidedelay').",\n";
//     $options .= "                                          captionAndToolbarFlipPosition: ".$cfg_captionandtoolbarflipposition.",\n";
//     $options .= "                                          slideshowDelay: ".$this->params->get('cfg_slideinterval').",\n";
//     $options .= "                                          autoStartSlideshow: ".$autoStartSlideshow.",\n";
//     $options .= "                                          enableMouseWheel: true,\n";
//     $options .= "                                          enableKeyboard: true,\n";
//     $options .= "                                        });\n";

    $options  = "var jg_pswp_options = {\n";
    $options .= "  loop: ".$loop.",\n";
    $options .= "  captionEl: ".$captionEl.",\n";
    $options .= "  shareEl: ".$shareEl.",\n";
    $options .= "  downloadAllowed: ".($this->_downloadAllowed ? 'true' : 'false').",\n";
    $options .= "  isMobile: ".($this->_isMobile ? 'true' : 'false')."\n";
    $options .= "};\n";

    $this->_doc->addScriptDeclaration($options);

  }

  /**
   * onJoomOpenImage method (override)
   *
   * Method is called when an image of JoomGallery shall be opened.
   * It modifies the given link in order to use the respective box for opening the image.
   *
   * @access  public
   * @param   string  $link     The link to modify
   * @param   object  $image    An object holding the image data
   * @param   string  $img_url  The URL to the image which shall be openend
   * @param   string  $group    The name of an image group, most boxes will make an album out of the images of a group
   * @param   string  $type     'orig' for original image, 'img' for detail image or 'thumb' for thumbnail
   * @param   string  $selected If a specific box was selected it will be delivered in this argument
   * @return  void
   */
  public function onJoomOpenImage(&$link, $image = null, $img_url = null, $group = 'joomgallery', $type = 'orig', $selected = null)
  {
    if(!$image)
    {
      // Let JoomGallery know that this plugin is enabled (this is for backwards compatibility only)
      $link = true;

      return;
    }

    if($selected && $selected != $this->title)
    {
      // If an OpenImage plugin was selected but not this one we don't do anything here
      return;
    }

    if(is_null($this->_isMobile))
    {
      // Include class for mobile device detection instead of using JBrowser
      if(!class_exists('Mobile_Detect'))
      {
        require_once dirname(__FILE__).'/Mobile_Detect.php';
      }

      // Check for a mobile device
      $detect = new Mobile_Detect;
      $this->_isMobile = $detect->isMobile();
    }

    $this->_jg_config = JoomConfig::getInstance();

    // Check whether we are in JoomGallery detail view
    $isDetailView = JFactory::getApplication()->input->getCmd('view') === 'detail' ? true : false;

    // Check for non mobile devices whether this popup box is selected in JoomGallery's category
    // view with the option set to open the detail view for non mobile devices. If that is the
    // case and this box is also selected to show the original images in detail view set a helper flag.
    $showMeInDetailView = false;

    if(    $isDetailView
        && !$this->_isMobile
        && $this->params->get('cfg_openimage') == 0
        && $this->_jg_config->get('jg_bigpic_open') === $this->title)
    {
      $showMeInDetailView = true;
    }

    if(is_null($this->_downloadAllowed)) {
      $this->_downloadAllowed = false;

      if($this->_jg_config->get('jg_download') && (JFactory::getUser()->get('id') || $this->_jg_config->get('jg_download_unreg'))) {
        $this->_downloadAllowed = true;
      }
    }

    if(!$this->_isMobile && $this->params->get('cfg_openimage') != $this->title && !$showMeInDetailView)
    {
      $link = JHtml::_('joomgallery.openimage', $this->params->get('cfg_openimage'), $image, $type, $group);

      return;
    }

    parent::onJoomOpenImage($link, $image, $img_url, $group, $type, $selected);
  }

  /**
   * This method sets an associative array of attributes for the 'a'-Tag (key/value pairs)
   * which opens the image and adds some image group specific JavaScript code for PhotoSwipe.
   *
   * @param   array   $attribs  Associative array of HTML attributes which you have to fill
   * @param   object  $image    An object holding all the relevant data about the image to open
   * @param   string  $img_url  The URL to the image which shall be openend
   * @param   string  $group    The name of an image group, most popup boxes are able to group the images with that
   * @param   string  $type     'orig' for original image, 'img' for detail image or 'thumb' for thumbnail
   * @return  void
   */
  protected function getLinkAttributes(&$attribs, $image, $img_url, $group, $type)
  {
    // Prepare image size
    $imgsize   = getimagesize($this->_jg_ambit->getImg($type.'_path', $image));
    $data_size = $imgsize[0].'x'.$imgsize[1];

    // Prepare the caption
    $data_title = '';
    if(!$this->params->get('cfg_captionhide'))
    {
      if($this->_jg_config->get('jg_show_title_in_popup'))
      {
        $data_title .= $image->imgtitle;
      }
      if($this->_jg_config->get('jg_show_description_in_popup') && !empty($image->imgtext))
      {
        if(!empty($data_title))
        {
          if(!$this->_isMobile)
          {
            $data_title .= ' : ';
          }
          else
          {
            $data_title .= ' :: ';
          }
        }

        if($this->_jg_config->get('jg_show_description_in_popup') == 2)
        {
          $data_title .= htmlspecialchars(strip_tags($image->imgtext));
        }
        else
        {
          $data_title .= htmlspecialchars($image->imgtext);
        }
      }
    }

    // Prepare the link attributes
    $attribs['rel']          = $this->title.'-'.$group;
    $attribs['data-group']   = $group;
    $attribs['data-title']   = $data_title;
    $attribs['data-size']    = $data_size;

    if($this->params->get('cfg_shareshow') && $this->params->get('cfg_sharepageurl') == 1)
    {
      $attribs['data-share_page_url'] = JUri::getInstance()->toString(array('scheme', 'host', 'port')) . JRoute::_('index.php?option=' . _JOOM_OPTION . '&view=detail&id=' . $image->id . $this->_jg_ambit->getItemid(true));
    }

    // Download URL
    if($this->params->get('cfg_shareshow') && $this->_downloadAllowed)
    {
      $attribs['data-share_download_url'] = JUri::getInstance()->toString(array('scheme', 'host', 'port')) . JRoute::_('index.php?option=' . _JOOM_OPTION . '&task=download&id=' . $image->id);
    }
  }
}