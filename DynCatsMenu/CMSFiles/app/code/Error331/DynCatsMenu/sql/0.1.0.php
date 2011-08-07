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

class Error331_DynCatsMenu_Upgrade_0_1_0 extends Axis_Core_Model_Migration_Abstract
{
    protected $_version = '0.1.0';
    protected $_info = 'install';

    public function up()
    {       
    Axis::single('core/config_field')->add('design/DynCatsMenu/CatsPerLine', 'Categories per line', '5');  
    Axis::single('core/config_field')->add('design/DynCatsMenu/BrandsPerCat', 'Brands per category', '2');         
    Axis::single('core/config_field')->add('design/DynCatsMenu/TitleCharsCount', 'Title chars count', '30');     
    }

    public function down()
    {   
    Axis::single('core/config_field')->remove('design/DynCatsMenu/CatsPerLine'); 
    Axis::single('core/config_field')->remove('design/DynCatsMenu/BrandsPerCat'); 
    Axis::single('core/config_field')->remove('design/DynCatsMenu/TitleCharsCount');     
    }
}