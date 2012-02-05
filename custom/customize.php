<?php
// GPL

/**
 * This script helps you to customize a Simple Groupware installation and to keep your changes after running updates.
 *
 * More information about customizing Simple Groupware can be found at:
 * http://www.simple-groupware.de/cms/Customization
 *
 * Using this customization method helps you to:
 * - keep your changes separated to the standard Simple Groupware code
 * - persist your changes when doing an update
 *
 * This script is run when installing or updating Simple Groupware.
 *
 * These commands can be used to change the Simple Groupware code base:
 *
 * - Append code:
 *   setup::customize_replace($file,$code_before,$code_before.$append_code);
 *
 * - Replace code:
 *   setup::customize_replace($file,$code_old,$code_new);
 *
 * - Remove code:
 *   setup::customize_replace($file,$code_remove,"");
 * 
 * Examples:
 *
 * First create two modules under "<sgs-dir>/custom/modules/schema/new_module.xml" and
 * "<sgs-dir>/custom/modules/schema/news2.xml".
 *
 * Then add the modification commands to this file (\n = line break):
 *
 * // add a new module to module list
 * setup::customize_replace("modules/schema/modules.txt", "wiki|Wiki", "wiki|Wiki"."\n"."new_module|My new module");
 *
 * // replace a module in the module list
 * setup::customize_replace("modules/schema/modules.txt", "\n"."news|News","\n"."news2|News2");
 *
 * // remove the Wiki module from the module list
 * setup::customize_replace("modules/schema/modules.txt", "\nwiki|Wiki","");
 *
 * Tip: Never forget to document your changes!
 */