<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_FieldButton_Submit extends JCckPluginField
{
	protected static $type		=	'button_submit';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}

		if ( isset( $data['json']['options2']['task'] ) ) {
			$data['json']['options2']['task_id']		=	'';
			$task										=	$data['json']['options2']['task'];
			if ( $task == 'export' || $task == 'process' ) {
				$data['json']['options2']['task_id']	=	$data['json']['options2']['task_id_'.$task];
				unset( $data['json']['options2']['task_id_export'] );
				unset( $data['json']['options2']['task_id_process'] );
			}
		}
		parent::g_onCCK_FieldConstruct( $data );
	}

	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array() )
	{
		$data['live']		=	NULL;
		$data['validation']	=	NULL;
		$data['variation']	=	array( JHtml::_( 'select.option', 'hidden', JText::_( 'COM_CCK_HIDDEN' ) ),
									   JHtml::_( 'select.option', 'value', JText::_( 'COM_CCK_VALUE' ) ),
									   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_FORM' ) ),
									   JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
									   JHtml::_( 'select.option', 'disabled', JText::_( 'COM_CCK_FORM_DISABLED2' ) ),
									   JHtml::_( 'select.option', '</OPTGROUP>', '' ),
									   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_TOOLBAR' ) ),
									   JHtml::_( 'select.option', 'toolbar_button', JText::_( 'COM_CCK_TOOLBAR_BUTTON' ) ),
									   JHtml::_( 'select.option', '</OPTGROUP>', '' ) );

		parent::onCCK_FieldConstruct_TypeForm( $field, $style, $data );
	}

	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array() )
	{
		$data['live']		=	NULL;
		$data['match_mode']	=	NULL;
		$data['validation']	=	NULL;
		$data['variation']	=	array( JHtml::_( 'select.option', 'hidden', JText::_( 'COM_CCK_HIDDEN' ) ),
									   JHtml::_( 'select.option', 'value', JText::_( 'COM_CCK_VALUE' ) ),
									   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_FORM' ) ),
									   JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
									   JHtml::_( 'select.option', 'disabled', JText::_( 'COM_CCK_FORM_DISABLED2' ) ),
									   JHtml::_( 'select.option', '</OPTGROUP>', '' ),
									   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_TOOLBAR' ) ),
									   JHtml::_( 'select.option', 'toolbar_button', JText::_( 'COM_CCK_TOOLBAR_BUTTON' ) ),
									   JHtml::_( 'select.option', '</OPTGROUP>', '' ) );

		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path		=	parent::g_getPath( self::$type.'/' );
		$field->label2	=	trim( @$field->label2 );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value			=	$field->label;
		$field->label	=	'';
		
		// Prepare
		$pre_task	=	'';
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$task		=	( isset( $options2['task'] ) && $options2['task'] ) ? $options2['task'] : 'save';
		$task_id	=	( isset( $options2['task_id'] ) && $options2['task_id'] ) ? $options2['task_id'] : 0;
		if ( JFactory::getApplication()->isAdmin() ) {
			$task	=	( $config['client'] == 'admin' ) ? 'form.'.$task : 'list.'.$task;
		}
		if ( $task_id ) {
			$pre_task	=	htmlspecialchars( 'jQuery("#'.$config['formId'].'").append(\'<input type="hidden" name="tid" value="'.$task_id.'">\');' );
		}
		$class		=	'button btn' . ( $field->css ? ' '.$field->css : '' );
		if ( $task == 'cancel' ) {
			$click	=	' onclick="Joomla.submitform(\''.$task.'\', document.getElementById(\'seblod_form\'));"';
		} else {
			if ( $task == 'export' ) {
				parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$field->name, 'task'=>$task, 'task_id'=>$task_id ) );
			} elseif ( $task == 'save2redirect' ) {
				$custom		=	'';
				if ( isset( $options2['custom'] ) && $options2['custom'] ) {
					$custom	=	JCckDevHelper::replaceLive( $options2['custom'] );
					$custom	=	$custom ? '&'.$custom : '';
				}
				if ( $config['client'] == 'search' ) {
					$pre_task	=	htmlspecialchars( 'jQuery("#'.$config['formId'].'").attr(\'action\', \''.JRoute::_( 'index.php?Itemid='.$options2['itemid'].$custom ).'\');' );
				} else {
					$pre_task	=	htmlspecialchars( 'jQuery("#'.$config['formId'].' input[name=\'config[url]\']").val(\''.JRoute::_( 'index.php?Itemid='.$options2['itemid'].$custom ).'\');' );
				}
			}
			$click		=	isset( $config['submit'] ) ? ' onclick="'.$pre_task.$config['submit'].'(\''.$task.'\');return false;"' : '';	
		}
		// $click	=	isset( $config['formId'] ) ? ' onclick="if (document.'.$config['formId'].'.boxchecked.value==0){alert(\''.addslashes( JText::_( 'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST' ) ).'\');}else{ Joomla.submitbutton(\''.$task.'\')};return false;"' : '';
		if ( $field->attributes && strpos( $field->attributes, 'onclick="' ) !== false ) {
			$matches	=	array();
			$search		=	'#onclick\=\"([a-zA-Z0-9_\(\)\\\'\;\.]*)"#';
			preg_match( $search, $field->attributes, $matches );
			if ( count( $matches ) && $matches[0] ) {
				if ( $matches[0] == $field->attributes ) {
					$field->attributes	=	substr( trim( $field->attributes ), 0, -1 );
					$click				=	' '.$field->attributes.'"';
					$field->attributes	=	'';
				} else {
					$click				=	' onclick="'.$matches[1].'"';
					$field->attributes	=	trim( str_replace( $matches[0], '', $field->attributes ) );
				}
			}
		}
		$attr		=	'class="'.$class.'"'.$click . ( $field->attributes ? ' '.$field->attributes : '' );
		if ( $field->bool ) {
			$label	=	$value;
			if ( JCck::on() ) {
				if ( $field->bool6 == 3 ) {
					$label		=	'<span class="icon-'.$options2['icon'].'"></span>';
					$attr		.=	' title="'.$value.'"';
				} elseif ( $field->bool6 == 2 ) {
					$label		=	$value."\n".'<span class="icon-'.$options2['icon'].'"></span>';
				} elseif ( $field->bool6 == 1 ) {
					$label		=	'<span class="icon-'.$options2['icon'].'"></span>'."\n".$value;
				}
			}
			$type	=	( $field->bool7 == 1 ) ? 'submit' : 'button';
			$form	=	'<button type="'.$type.'" id="'.$id.'" name="'.$name.'" '.$attr.'>'.$label.'</button>';
			$tag	=	'button';
		} else {
			$form	=	'<input type="submit" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />';
			$tag	=	'input';
		}
		if ( $field->bool2 == 1 ) {
			$alt	=	$field->bool3 ? ' '.JText::_( 'COM_CCK_OR' ).' ' : "\n";
			if ( $config['client'] == 'search' ) {
				$onclick	=	'onclick="jQuery(\'#'.$config['formId'].'\').clearForm();"';
				$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.JText::_( 'COM_CCK_RESET' ).'">'.JText::_( 'COM_CCK_RESET' ).'</a>';				
			} else {
				$onclick	=	'onclick="Joomla.submitform(\'cancel\', document.getElementById(\'seblod_form\'));"';
				$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.JText::_( 'COM_CCK_CANCEL' ).'">'.JText::_( 'COM_CCK_CANCEL' ).'</a>';
			}
		} elseif ( $field->bool2 == 2 ) {
			$alt		=	$field->bool3 ? ' '.JText::_( 'COM_CCK_OR' ).' ' : "\n";
			$field2		=	(object)array( 'link'=>$options2['alt_link'], 'link_options'=>$options2['alt_link_options'], 'id'=>$id, 'name'=>$name, 'text'=>htmlspecialchars( $options2['alt_link_text'] ), 'value'=>'' );
			JCckPluginLink::g_setLink( $field2, $config );
			JCckPluginLink::g_setHtml( $field2, 'text' );
			$form		.=	$alt.$field2->html;
		}
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			if ( $field->variation == 'toolbar_button' ) {
				$field->form	=	'';
				$icon			=	( isset( $options2['icon'] ) && $options2['icon'] ) ? 'icon-'.$options2['icon'] : '';
				$html			=	'<button class="btn btn-small'.( $field->css ? ' '.$field->css : '' ).'" onclick="'.$pre_task.'JCck.Core.submit(\''.$task.'\')" href="#"><i class="'.$icon.'"></i> '.$value.'</button>';
				JToolBar::getInstance( 'toolbar' )->appendButton( 'Custom', $html, @$options2['icon'] );
				// JToolBar::getInstance( 'toolbar' )->appendButton( 'Standard', $options2['icon'], $value, $task, true ); todo: check
			} else {
				parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<'.$tag, ' ', '', $config );
			}
		}
		$field->value	=	'';
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Prepare
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_BeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		if ( $process['task'] == 'export' ) {
			if ( isset( $config['ids'] ) && $config['ids'] != '' ) {
				$name					=	$process['name'];
				$search					=	'onclick="';
				$replace				=	$search.htmlspecialchars( 'jQuery("#'.$config['formId'].'").append(\'<input type="hidden" name="ids" value="'.$config['ids'].'">\');' );
				$fields[$name]->form	=	str_replace( $search, $replace, $fields[$name]->form );
			}
		}
	}
}
?>