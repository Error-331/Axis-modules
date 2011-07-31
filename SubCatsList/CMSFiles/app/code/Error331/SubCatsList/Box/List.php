<?php

/**
 * SubCatsList
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
           
class Error331_SubCatsList_Box_List extends Axis_Core_Box_Abstract
  {
  protected $CurCatsPerLine = 5;
  protected $CurBrandsPerCat = 2;
  protected $CurTitleCharsCount = 30;
  
  protected $CurSubCatLevel = NULL;
  
  protected $_disableWrapper = true;

  public function _beforeRender()
    {
    $tmpCatsPerLine = Axis::config('design/SubCatsList/CatsPerLine'); 
    $tmpBrandsPerCat = Axis::config('design/SubCatsList/BrandsPerCat'); 
    $tmpTitleCharsCount = Axis::config('design/SubCatsList/TitleCharsCount'); 
    
    if (is_null($tmpCatsPerLine) !== TRUE)
      {
      $tmpCatsPerLine = intval($tmpCatsPerLine);
      if ($tmpCatsPerLine > 0){$this->CurCatsPerLine = $tmpCatsPerLine;}
      }
      
    if (is_null($tmpBrandsPerCat) !== TRUE)
      {
      $tmpBrandsPerCat = intval($tmpBrandsPerCat);
      if ($tmpBrandsPerCat > 0){$this->CurBrandsPerCat = $tmpBrandsPerCat;}
      }
    
    if (is_null($tmpTitleCharsCount) !== TRUE)
      {
      $tmpTitleCharsCount = intval($tmpTitleCharsCount);
      if ($tmpTitleCharsCount > 0){$this->CurTitleCharsCount = $tmpTitleCharsCount;}
      }    
        
    if (!Zend_Registry::isRegistered('catalog/current_category')) {return false;}
               
    $tmpCurCat = Zend_Registry::get('catalog/current_category');
    $tmpQuery = Axis::model('catalog/category')->select('*')
                ->joinInner
                  (
                  'catalog_category_description',
                  'cc.id = ccd.category_id',
                  '*'
                  )   
                ->joinInner
                  (
                  'catalog_hurl',
                  'cc.id = ch.key_id',
                  'key_word'
                  )
                ->where("ch.key_type='c'")
                ->where('cc.site_id = ?', Axis::getSiteId())
                ->where('cc.lft > ?', $tmpCurCat->lft)
                ->where('cc.rgt < ?', $tmpCurCat->rgt)
                ->where('ccd.language_id = ?', Axis_Locale::getLanguageId())
                ->order('cc.lft');

    $this->CurSubCatLevel = $tmpCurCat->lvl + 1;
    $tmpSubCats = $tmpQuery->fetchAll();
        
    if (!$tmpSubCats) 
      {
      return false;
      }
    else
      {
      $tmpManCache = Axis::single('catalog/product_manufacturer')->cache()->getList();

      if (!count($tmpManCache)) 
        {
        $this->categories = $tmpSubCats;
        return true;
        }            
      
      for ($Counter1 = 0; $Counter1 < count($tmpSubCats); $Counter1++) 
        {
        $tmpQuery = Axis::model('catalog/product_category')->select('product_id')
                    ->joinInner
                      (
                      'catalog_product',
                      'cp.id = cpc.product_id',
                      'manufacturer_id'
                      )   
                   ->joinInner
                      (
                      'catalog_product_manufacturer',
                      'cpm.id = cp.manufacturer_id',
                      'name'
                      )  
                   ->joinInner
                      (
                      'catalog_product_manufacturer_description',
                      'cpmd.manufacturer_id = cpm.id',
                      'title'
                      ) 
                    ->where('cpc.category_id="'.$tmpSubCats[$Counter1]['id'].'"')
                    ->where('cpmd.language_id = ?', Axis_Locale::getLanguageId())
                    ->group('cp.manufacturer_id'); 
                
        $tmpSubCats[$Counter1]['add_info'] = $tmpQuery->fetchAll(); 
        
        if ($tmpSubCats[$Counter1]['add_info'])
          {
          for($Counter2 = 0; $Counter2 < count($tmpSubCats[$Counter1]['add_info']); $Counter2++)
            {
            $tmpSubCats[$Counter1]['add_info'][$Counter2]['man_url'] = NULL;
            
            for ($Counter3 = 0; $Counter3 < count($tmpManCache); $Counter3++)
              {
              if ($tmpSubCats[$Counter1]['add_info'][$Counter2]['manufacturer_id'] == $tmpManCache[$Counter3]['id'])
                {
                $tmpSubCats[$Counter1]['add_info'][$Counter2]['man_url'] = $tmpManCache[$Counter3]['url'];
                break;
                }
              }
            }
          }      
        }
        
      $this->categories = $tmpSubCats;
      return true;
      }
    }
    
  public function GetCatsPerLine()
    {
    return $this->CurCatsPerLine;
    }  
        
  public function GetBrandsPerCat()
    {
    return $this->CurBrandsPerCat;
    }
    
  public function GetTitleCharsCount()
    {
    return $this->CurTitleCharsCount;
    }    

  public function GetSubCatLevel()
    {
    return $this->CurSubCatLevel;
    }
    
  public function GetHURL($usrCatInfo, $usrCont)
    {
    $tmpURL = $usrCont->hurl(array(
              'cat' => array(
              'value' => $usrCatInfo['id'],
              'seo'   => $usrCatInfo['key_word']
              ),
              'controller'    => 'catalog',
              'action'        => 'view'
              ), false/*non-ssl link*/, true/*reset url params*/);  
  
    return $tmpURL;
    }
    
  public function GetMHURL($usrManInfo, $usrCont)
    {
    $tmpURL = $usrCont->hurl(array(
        'controller' => 'catalog',
        'action' => 'view',
        'manufacturer' => array(
            'value' => $usrManInfo['manufacturer_id'], 
            'seo' => $usrManInfo['man_url']
        )
    ), false, true);
  
    return $tmpURL;
    }
  }