<?php

/**
 * DynCatsMenu
 * 
 * NOTICE OF LICENSE 
 * 
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (Version 3)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to red331@mail.ru so we can send you a copy immediately.   
 *  
 * author      Selihov Sergei Stanislavovich <red331@mail.ru>, Valentin Belyakov <nipellio@gmail.com> 
 * copyright   Copyright (c) 2010-2011 Selihov Sergei Stanislavovich.
 * license     http://www.gnu.org/licenses/gpl.html  GNU GENERAL PUBLIC LICENSE (Version 3)
 *    
 */
           
class Error331_DynCatsMenu_Box_Menu extends Axis_Core_Box_Abstract
  {
  protected $_disableWrapper = true;

  public function _beforeRender()
    {
    $tmpCatList = Axis::model('catalog/category')->getNestedTreeData();                                   
    
    if (is_array($tmpCatList) === TRUE)
      {
      $this->cattree = $tmpCatList;
      return true;
      }
    else
      {
      $this->cattree = false;
      return false;
      }
    }
    
  public function GetCatHURLById($usrId)
    {
    if (!$usrId) 
      {
      return false;
      }
      
    return Axis::model('catalog/category')->select('*')
                                          ->joinLeft(
                                          'catalog_hurl',
                                          "ch.key_id = cc.id AND ch.key_type = 'c'",
                                          'key_word'
                                          )
                                          ->where('cc.id = ?', $usrId)
                                          ->fetchRow();
    } 
  }