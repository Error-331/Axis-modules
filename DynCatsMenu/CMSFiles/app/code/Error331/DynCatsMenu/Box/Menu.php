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
    
  public function FormatSubCats($usrCats, $usrCatId)
    {
    $tmpCurLevel = NULL;
    $tmpArr = array();
                          
    $Counter1 = 0;
    $Counter2 = 0;
                          
    for($Counter1 = 0; $Counter1 < count($usrCats); $Counter1++)
      {
      $usrCats[$Counter1]['is_part'] = TRUE;  
    
      if ($usrCats[$Counter1]['lvl'] == 1)
        {
			  $usrCats[$Counter1]['sub_mark'] = 'DCMSub0 DCMNotShow';
			  }
      else if ($usrCats[$Counter1]['lvl'] == 2)
        {
        $usrCats[$Counter1]['sub_mark'] = 'DCMSub2_1 DCMNotShow';	
        }
      else
        {
        $usrCats[$Counter1]['sub_mark'] = 'DCMDeepSub DCMNotShow';
        }					
                            
      if ($usrCats[$Counter1]['id'] === $usrCatId)
        {
        $tmpCurLevel = $usrCats[$Counter1]['lvl'];
        $tmpArr[] = $usrCats[$Counter1]['lvl'];
        $usrCats[$Counter1]['is_selected'] = TRUE;
        $usrCats[$Counter1]['is_part'] = TRUE;                            
                              
        if ($usrCats[$Counter1]['lvl'] == 1)
          {
          $usrCats[$Counter1]['sub_mark'] = 'DCMSub1 DCMShow';
          }
        else if ($usrCats[$Counter1]['lvl'] == 2)
          {
          $usrCats[$Counter1]['is_selected'] = TRUE;
          $usrCats[$Counter1]['is_part'] = TRUE;
                                
          for ($Counter2 = $Counter1; $Counter2 >= 0; $Counter2--)
            {
            if ($usrCats[$Counter2]['lvl'] != $tmpCurLevel && in_array($usrCats[$Counter2]['lvl'], $tmpArr) === FALSE)
              {
              $tmpArr[] = $usrCats[$Counter2]['lvl'];
              $usrCats[$Counter2]['is_part'] = TRUE;
                                    
              if ($usrCats[$Counter2]['lvl'] == 1)
                {
                $usrCats[$Counter2]['sub_mark'] = 'DCMSub1 DCMShow';
                }                                                                        
              }                                                                 
            }
                                  
        $usrCats[$Counter1]['sub_mark'] = 'DCMSub2 DCMShow';
        }
      else if ($usrCats[$Counter1]['lvl'] > 2)
        {
        for ($Counter2 = $Counter1; $Counter2 >= 0; $Counter2--)
          {
          if ($usrCats[$Counter2]['lvl'] != $tmpCurLevel && in_array($usrCats[$Counter2]['lvl'], $tmpArr) === FALSE)
            {
            $tmpArr[] = $usrCats[$Counter2]['lvl'];
            $usrCats[$Counter2]['is_part'] = TRUE;
                                    
            if ($usrCats[$Counter2]['lvl'] == 1)
              {
              $usrCats[$Counter2]['sub_mark'] = 'DCMSub1 DCMShow';
              }                                  
            else if ($usrCats[$Counter2]['lvl'] == 2)
              {
              $usrCats[$Counter2]['sub_mark'] = 'DCMSub2 DCMShow';
              }
            }
          }
        }
      }                              
    else
      {
      $usrCats[$Counter1]['is_selected'] = FALSE;
      }
    }
                            
  return $usrCats;
  }
    
  public function ShowCatsRec($usrCats, $usrCatNum, $usrCont, $usrIsFinal = FALSE)
    {
    $tmpCatHurl = NULL;
    $tmpCurLevel = 0;
    $Counter1 = 0;
                          
    $tmpCurLevel = $usrCats[$usrCatNum]['lvl'];
                                                                                                 
    if ($tmpCurLevel <= $usrCats[$usrCatNum-1]['lvl'])
      {
      return FALSE;
      }
                          
    ?> <ul> <?php
                                                    
    for($Counter1 = $usrCatNum; $Counter1 < count($usrCats); $Counter1++)
      {
      if ($usrCats[$Counter1]['lvl'] > $tmpCurLevel)
        {
        continue;
        }
      else if ($usrCats[$Counter1]['lvl'] < $tmpCurLevel)
        {
        echo('</ul>');
        return TRUE;
        }
                            
      $tmpCatHurl = $this->GetCatHURLById($usrCats[$Counter1]['id']); 
                                  
      if (isset($usrCats[$Counter1]['sub_mark']) === TRUE && empty($usrCats[$Counter1]['sub_mark']) !== TRUE)
        {
        echo('<li class="'.$usrCats[$Counter1]['sub_mark'].'"><a href="'.$usrCont->href('/').'store/'.$tmpCatHurl['key_word'].'/">'.$usrCats[$Counter1]['name'].'</a>');                                
        }
      else
        {
        echo('<li><a href="'.$usrCont->href('/').'store/'.$tmpCatHurl['key_word'].'/">'.$usrCats[$Counter1]['name'].'</a>');
        } 
                            
      if ($Counter1+1 < count($usrCats))
        {
        if ($usrCats[$Counter1+1]['lvl'] > $tmpCurLevel && $usrIsFinal === FALSE && $usrCats[$Counter1]['is_part'] && $usrIsFinal === FALSE)
          {
				  $this->ShowCatsRec($usrCats, $Counter1+1, $usrCont, FALSE);
          }
        }
                              
      echo('</li>');                             
      } 
                            
    ?> </ul> <?php 
                          
    return TRUE; 
    }
    
public function ShowCats($usrCats, $usrCont)
    {
    $Counter1 = 0;                          
                                                    
    $tmpIsCatSel = FALSE;
    $tmpCatHurl = NULL;
                                                    
    /* Subcategories info get starts here */
                          
    if (!Zend_Registry::isRegistered('catalog/current_category')) 
      {
      $tmpIsCatSel = FALSE;
      $usrCats = $this->FormatSubCats($usrCats, 0);
      }
    else
      {
      $tmpIsCatSel = Zend_Registry::get('catalog/current_category');
      $usrCats = $this->FormatSubCats($usrCats, $tmpIsCatSel->id);
      }
                          
    /* Subcategories ingo get ends here */
                                                                                             
    ?>                       
    <div class="DCMContOut">
     <div class="DCMContHead"><span class="DCMTitle"><?php echo Axis::translate('Error331_DynCatsMenu')->__('Product Categories'); ?></span></div>
	    <div class="DCMBCont">
       <ul>
                            
       <?php
                             
       for ($Counter1 = 0; $Counter1 < count($usrCats); $Counter1++)
        {
        if ($usrCats[$Counter1]['lvl'] != 1)
          {
          continue;
          }  
                                 
        $tmpCatHurl = $this->GetCatHURLById($usrCats[$Counter1]['id']); 
                           
        echo('<li class="'.$usrCats[$Counter1]['sub_mark'].'"><a href="'.$usrCont->href('/').'store/'.$tmpCatHurl['key_word'].'/">'.$usrCats[$Counter1]['name'].'</a>');
                                  
        if ($Counter1+1 < count($usrCats))
          {
          $this->ShowCatsRec($usrCats, $Counter1+1, $usrCont, FALSE);
          }                                 
                                  
        echo('</li>');                                                          
        }
                             
      ?>
                            
      </ul>                         
     </div>
    </div>
                    
    <?php 
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