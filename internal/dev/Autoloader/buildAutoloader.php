<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 04.11.2015
 * Time: 10:08
 */
const BUILD_AUTOLOADER_ACTION = 'build';
include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/vendor/autoload.php');
//include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/plugins/mustache.php-master/src/Mustache/Autoloader.php');
//$mustacheAutoloader = new Mustache_Autoloader();
//$mustacheAutoloader->register();
include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/dc/common/interfaces/TemplateEngine.php');
include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/dc/common/classes/MustacheTemplateEngine.php');
include('AutoloaderBuilder.php');
$autoloaderBuilder = new AutoloaderBuilder(new \DynCom\dc\common\classes\MustacheTemplateEngine());


if(array_key_exists('action',$_REQUEST) && $_REQUEST['action'] === BUILD_AUTOLOADER_ACTION) {
    $paths = [];
    $path_errors = [];
    $target_classname = '';
    $target_classname_error = '';
    $target_path = '';
    $target_path_error = '';
    $in_error = false;
    if (array_key_exists('source_paths', $_REQUEST) && is_array($_REQUEST['source_paths'])) {
        foreach ($_REQUEST['source_paths'] as $source_path) {
            $path_str = filter_var($source_path, FILTER_SANITIZE_STRING);
            if(!empty($path_str)) {
                if (is_dir($path_str) || is_dir(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/' . $path_str)) {
                    $paths[] = $path_str;
                } else {
                    $path_errors[] = 'Path \'' . $path_str . '\' is not a valid path';
                    $in_error = true;
                }
            }
        }
    } else {
        $path_errors[] = 'No source paths given.';
        $in_error = true;
    }
    if (array_key_exists('target_classname', $_REQUEST)) {
        $target_classname = filter_var($_REQUEST['target_classname'], FILTER_SANITIZE_STRING);
    } else {
        $target_classname_error = 'No target classname given.';
        $in_error = true;
    }
    if (array_key_exists('target_path', $_REQUEST)) {
        $target_path_str = filter_var($_REQUEST['target_path'], FILTER_SANITIZE_STRING);
        if (is_dir($target_path_str) || is_dir(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/' . $target_path_str)) {
            $target_path = $target_path_str;
        } else {
            $target_path_error = 'Target path \'' . $target_path_str . '\'is not a valid path';
            $in_error = true;
        }
    } else {
        $target_path_error = 'No target path given.';
        $in_error = true;
    }


    if ($in_error) {
        echo "The action could not be completed because of the following errors" . PHP_EOL;
        foreach ($path_errors as $path_error) {
            echo "SOURCE PATHS: " . $path_error . PHP_EOL;
        }
        if ($target_classname_error !== '') {
            echo "TARGET CLASSNAME: " . $target_classname_error . PHP_EOL;
        }
        if ($target_path_error !== '') {
            echo "TARGET CLASSNAME: " . $target_path_error . PHP_EOL;
        }
    } else {
        $autoloader_creation_successful = $autoloaderBuilder->build($paths, $target_classname, $target_path);
        $success_message = 'Autoloader \'' . $target_classname . '\' has been successfully created';
        $failure_message = 'Autoloader \'' . $target_classname . '\' could not be created successfully';
        if($autoloader_creation_successful) {
            echo $success_message;
        } else {
            echo $failure_message;
        }
    }
} else {
    include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/dc/common/classes/FormElement.php');
    include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/dc/common/classes/Form.php');
    include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/dc/common/classes/FormBuilder.php');

    $form_options = [
        'method' => 'get'
    ];
    $fb = new \DynCom\dc\common\classes\FormBuilder('autoloader_builder_form',$form_options);
    $inner_wrapper_id = 'auloader_builder_inner_wrapper';
    $path_wrapper_id = 'source_path_wrapper';
    $source_path_wrapper_id = 'source_path_wrapper';
    $target_name_wrapper_id = 'target_class_name_wrapper';
    $target_path_wrapper_id = 'target_path_wrapper';
    $submit_button_id = 'submit_button';
    $form_wrapper_class = 'form_wrapper';
    $form_section_wrapper_class = 'form_section_wrapper';
    $text_input_long_class = 'text_input_long';
    $text_input_normal_class = 'text_input_normal';
    $submit_button_class = 'form_submit_button';
    $source_paths_name = 'source_paths';
    $target_name_name = 'target_classname';
    $target_path_name = 'target_path';
    $action_name = 'action';
    $source_paths_label = 'Source path';
    $target_name_label = 'Target classname';
    $target_path_label = 'Target path';
    $submit_button_label = 'Create autoloader';
    $action_value = 'build';

    $inner_wrapper = $fb->addDIV($inner_wrapper_id);
    $path_wrapper = $fb->addDIV($source_path_wrapper_id,$form_wrapper_class,$inner_wrapper_id);
    $fb->addHidden($action_name,$action_value);
    $fb->addTextInput($source_paths_name . '[0]',$source_paths_name . '_1',$text_input_long_class,$source_path_wrapper_id,null,$source_paths_label . ' 1');
    $fb->addTextInput($source_paths_name . '[1]',$source_paths_name . '_2',$text_input_long_class,$source_path_wrapper_id,null,$source_paths_label . ' 2');
    $fb->addTextInput($source_paths_name . '[2]',$source_paths_name . '_3',$text_input_long_class,$source_path_wrapper_id,null,$source_paths_label . ' 3');
    $target_name_wrapper = $fb->addDIV($target_name_wrapper_id,$form_section_wrapper_class,$inner_wrapper_id);
    $fb->addTextInput($target_name_name,$target_name_name,$text_input_normal_class,$target_name_wrapper_id,null,$target_name_label);
    $target_path_wrapper = $fb->addDIV($target_path_wrapper_id,$form_section_wrapper_class,$inner_wrapper_id);
    $fb->addTextInput($target_path_name,$target_path_name,$text_input_long_class,$target_path_wrapper_id,null,$target_path_label);
    $fb->addSubmitButton($submit_button_id,$submit_button_class,$submit_button_label,$inner_wrapper_id);
    $fb->render();
    echo $fb->getRenderedString();
}