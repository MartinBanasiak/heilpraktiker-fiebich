<?php

class DcAutoloader
{
    private static $map = array (
  'LanguageFunctions' => 
  array (
    0 => '/dc/admin/edit_language_functions.php',
  ),
  'SiteFunctions' => 
  array (
    0 => '/dc/admin/edit_site_functions.php',
  ),
  'TemplateInserterBase' => 
  array (
    0 => '/dc/common/abstracts/TemplateInserterBase.php',
  ),
  'URLBase' => 
  array (
    0 => '/dc/common/abstracts/URLBase.php',
  ),
  'Address' => 
  array (
    0 => '/dc/common/classes/Address.php',
  ),
  'AddressCollection' => 
  array (
    0 => '/dc/common/classes/AddressCollection.php',
  ),
  'AddressConfig' => 
  array (
    0 => '/dc/common/classes/AddressConfig.php',
  ),
  'Adminmenu' => 
  array (
    0 => '/dc/common/classes/Adminmenu.php',
  ),
  'BasicControlFlowHandler' => 
  array (
    0 => '/dc/common/classes/BasicControlFlowHandler.php',
  ),
  'CollectionTypes' => 
  array (
    0 => '/dc/common/classes/CollectionTypes.php',
  ),
  'DOMNodeView' => 
  array (
    0 => '/dc/common/classes/DOMNodeView.php',
  ),
  'DOMTest' => 
  array (
    0 => '/dc/common/classes/DOMTemplateTest.php',
  ),
  'A' => 
  array (
    0 => '/dc/common/classes/DOMTemplateTest.php',
    1 => '/dc/common/tests/HookTest.php',
  ),
  'DOMViewModelTest' => 
  array (
    0 => '/dc/common/classes/DOMViewModelTest.php',
  ),
  'EMail' => 
  array (
    0 => '/dc/common/classes/EMail.php',
  ),
  'Encryption' => 
  array (
    0 => '/dc/common/classes/Encryption.php',
  ),
  'FlattenedGettableDecorator' => 
  array (
    0 => '/dc/common/classes/FlattenedGettableDecorator.php',
  ),
  'Form' => 
  array (
    0 => '/dc/common/classes/Form.php',
  ),
  'FormBuilder' => 
  array (
    0 => '/dc/common/classes/FormBuilder.php',
  ),
  'FormElement' => 
  array (
    0 => '/dc/common/classes/FormElement.php',
  ),
  'GeneralErrorExceptionHandling' => 
  array (
    0 => '/dc/common/classes/GeneralErrorExceptionHandling.php',
  ),
  'GenericCRUDObjectStorageUnitOfWork' => 
  array (
    0 => '/dc/common/classes/GenericCRUDObjectStorageUnitOfWork.php',
  ),
  'GenericDOMRepositoryListView' => 
  array (
    0 => '/dc/common/classes/GenericDOMRepositoryListView.php',
  ),
  'GenericDOMView' => 
  array (
    0 => '/dc/common/classes/GenericDOMView.php',
  ),
  'GenericObjectStorage' => 
  array (
    0 => '/dc/common/classes/GenericObjectStorage.php',
  ),
  'GenericPHTMLRepositoryListView' => 
  array (
    0 => '/dc/common/classes/GenericPHTMLRepositoryListView.php',
  ),
  'GenericPHTMLView' => 
  array (
    0 => '/dc/common/classes/GenericPHTMLView.php',
  ),
  'GenericPlainRepositoryListView' => 
  array (
    0 => '/dc/common/classes/GenericPlainRepositoryListView.php',
  ),
  'GenericPlainView' => 
  array (
    0 => '/dc/common/classes/GenericPlainView.php',
  ),
  'GenericServicePayload' => 
  array (
    0 => '/dc/common/classes/GenericServicePayload.php',
  ),
  'GenericServicePayloadBuilder' => 
  array (
    0 => '/dc/common/classes/GenericServicePayloadBuilder.php',
  ),
  'GenericView' => 
  array (
    0 => '/dc/common/classes/GenericView.php',
  ),
  'Hook' => 
  array (
    0 => '/dc/common/classes/Hook.php',
  ),
  'HTMLSnippetProvider' => 
  array (
    0 => '/dc/common/classes/HTMLSnippetProvider.php',
  ),
  'Language' => 
  array (
    0 => '/dc/common/classes/Language.php',
  ),
  'LanguageCollection' => 
  array (
    0 => '/dc/common/classes/LanguageCollection.php',
  ),
  'LanguageConfig' => 
  array (
    0 => '/dc/common/classes/LanguageConfig.php',
  ),
  'LanguageRepository' => 
  array (
    0 => '/dc/common/classes/LanguageRepository.php',
  ),
  'MustacheTemplateEngine' => 
  array (
    0 => '/dc/common/classes/MustacheTemplateEngine.php',
  ),
  'MySQLiQueryWrapper' => 
  array (
    0 => '/dc/common/classes/MySQLiQueryWrapper.php',
  ),
  'NativeSessionFlashMessageBag' => 
  array (
    0 => '/dc/common/classes/NativeSessionFlashMessageBag.php',
  ),
  'NAVDateFormulaManagement' => 
  array (
    0 => '/dc/common/classes/NAVDateFormulaManagement.php',
  ),
  'NewValidator' => 
  array (
    0 => '/dc/common/classes/NewValidator.php',
  ),
  'Page' => 
  array (
    0 => '/dc/common/classes/Page.php',
  ),
  'PageCollection' => 
  array (
    0 => '/dc/common/classes/PageCollection.php',
  ),
  'PageConfig' => 
  array (
    0 => '/dc/common/classes/PageConfig.php',
  ),
  'PageRepository' => 
  array (
    0 => '/dc/common/classes/PageRepository.php',
  ),
  'PDOQueryWrapper' => 
  array (
    0 => '/dc/common/classes/PDOQueryWrapper.php',
  ),
  'Registry' => 
  array (
    0 => '/dc/common/classes/Registry.php',
  ),
  'RememberMeHandlerService' => 
  array (
    0 => '/dc/common/classes/RememberMeHandlerService.php',
  ),
  'RenderableElementResolver' => 
  array (
    0 => '/dc/common/classes/RenderableElementResolver.php',
  ),
  'RenderableStringTransformer' => 
  array (
    0 => '/dc/common/classes/RenderableStringTransformer.php',
  ),
  'SalutationOptions' => 
  array (
    0 => '/dc/common/classes/SalutationOptions.php',
  ),
  'SelectionCriteriaHelper' => 
  array (
    0 => '/dc/common/classes/SelectionCriteriaHelper.php',
  ),
  'ServiceRequestID' => 
  array (
    0 => '/dc/common/classes/ServiceRequestID.php',
  ),
  'SessionHandlerService' => 
  array (
    0 => '/dc/common/classes/SessionHandlerService.php',
  ),
  'SessionStorage' => 
  array (
    0 => '/dc/common/classes/SessionStorage.php',
  ),
  'Site' => 
  array (
    0 => '/dc/common/classes/Site.php',
  ),
  'SiteCollection' => 
  array (
    0 => '/dc/common/classes/SiteCollection.php',
  ),
  'SiteConfig' => 
  array (
    0 => '/dc/common/classes/SiteConfig.php',
  ),
  'Siteparts' => 
  array (
    0 => '/dc/common/classes/Siteparts.php',
  ),
  'SiteRepository' => 
  array (
    0 => '/dc/common/classes/SiteRepository.php',
  ),
  'StdMD5PasswordCheckerStrategy' => 
  array (
    0 => '/dc/common/classes/StdMD5PasswordCheckerStrategy.php',
  ),
  'TemplateDefaultInserter' => 
  array (
    0 => '/dc/common/classes/TemplateDefaultInserter.php',
  ),
  'TemplateDOMInserter' => 
  array (
    0 => '/dc/common/classes/TemplateDOMInserter.php',
  ),
  'TemplateInserter' => 
  array (
    0 => '/dc/common/classes/TemplateInserter.php',
  ),
  'TemplateInserterFactory' => 
  array (
    0 => '/dc/common/classes/TemplateInserterFactory.php',
  ),
  'TemplateStringInserter' => 
  array (
    0 => '/dc/common/classes/TemplateStringInserter.php',
  ),
  'Templating' => 
  array (
    0 => '/dc/common/classes/Templating.php',
  ),
  'Translate' => 
  array (
    0 => '/dc/common/classes/Translate.php',
  ),
  'UnitOfWork' => 
  array (
    0 => '/dc/common/classes/UnitOfWork.php',
  ),
  'UploadedFileTypeCheckingService' => 
  array (
    0 => '/dc/common/classes/UploadedFileTypeCheckingService.php',
  ),
  'URL' => 
  array (
    0 => '/dc/common/classes/URL.php',
  ),
  'URLImmutable' => 
  array (
    0 => '/dc/common/classes/URLImmutable.php',
  ),
  'URLMaker' => 
  array (
    0 => '/dc/common/classes/URLMaker.php',
  ),
  'Validator' => 
  array (
    0 => '/dc/common/classes/Validator.php',
  ),
  'ViewFactory' => 
  array (
    0 => '/dc/common/classes/ViewFactory.php',
  ),
  'Visitor' => 
  array (
    0 => '/dc/common/classes/Visitor.php',
  ),
  'VisitorCollection' => 
  array (
    0 => '/dc/common/classes/VisitorCollection.php',
  ),
  'VisitorConfig' => 
  array (
    0 => '/dc/common/classes/VisitorConfig.php',
  ),
  'VisitorRepository' => 
  array (
    0 => '/dc/common/classes/VisitorRepository.php',
  ),
  'ArrayGettable' => 
  array (
    0 => '/dc/common/interfaces/ArrayGettable.php',
  ),
  'UserAuthenticationStrategy' => 
  array (
    0 => '/dc/common/interfaces/AuthStrategy.php',
  ),
  'CriteriaHelperInterface' => 
  array (
    0 => '/dc/common/interfaces/CriteriaHelperInterface.php',
  ),
  'CRUDObjectStorage' => 
  array (
    0 => '/dc/common/interfaces/CRUDObjectStorage.php',
  ),
  'DOMRepositoryListView' => 
  array (
    0 => '/dc/common/interfaces/DOMRepositoryListView.php',
  ),
  'DOMTemplate' => 
  array (
    0 => '/dc/common/interfaces/DOMTemplate.php',
  ),
  'DOMView' => 
  array (
    0 => '/dc/common/interfaces/DOMView.php',
  ),
  'Entity' => 
  array (
    0 => '/dc/common/interfaces/Entity.php',
  ),
  'FlattenedGettable' => 
  array (
    0 => '/dc/common/interfaces/FlattenedGettable.php',
  ),
  'GenericDBModelInterface' => 
  array (
    0 => '/dc/common/interfaces/GenericDBModelInterface.php',
  ),
  'GenericDBQueryWrapperInterface' => 
  array (
    0 => '/dc/common/interfaces/GenericDBQueryWrapperInterface.php',
  ),
  'GenericObjectStorageInterface' => 
  array (
    0 => '/dc/common/interfaces/GenericObjectStorageInterface.php',
  ),
  'GenericTemplateInserterInterface' => 
  array (
    0 => '/dc/common/interfaces/GenericTemplateInserterInterface.php',
  ),
  'GenericViewInterface' => 
  array (
    0 => '/dc/common/interfaces/GenericViewInterface.php',
  ),
  'HashingStrategy' => 
  array (
    0 => '/dc/common/interfaces/HashingStrategy.php',
  ),
  'IOCInterface' => 
  array (
    0 => '/dc/common/interfaces/IOCInterface.php',
  ),
  'Observer' => 
  array (
    0 => '/dc/common/interfaces/Observer.php',
  ),
  'PageableViewInterface' => 
  array (
    0 => '/dc/common/interfaces/PageableViewInterface.php',
  ),
  'PasswordCheckerInterface' => 
  array (
    0 => '/dc/common/interfaces/PasswordCheckerInterface.php',
  ),
  'PHTMLRepositoryListView' => 
  array (
    0 => '/dc/common/interfaces/PHTMLRepositoryListView.php',
  ),
  'PHTMLTemplate' => 
  array (
    0 => '/dc/common/interfaces/PHTMLTemplate.php',
  ),
  'PHTMLView' => 
  array (
    0 => '/dc/common/interfaces/PHTMLView.php',
  ),
  'PlainRepositoryListView' => 
  array (
    0 => '/dc/common/interfaces/PlainRepositoryListView.php',
  ),
  'PlainTemplate' => 
  array (
    0 => '/dc/common/interfaces/PlainTemplate.php',
  ),
  'PlainView' => 
  array (
    0 => '/dc/common/interfaces/PlainView.php',
  ),
  'Repository' => 
  array (
    0 => '/dc/common/interfaces/Repository.php',
  ),
  'RepositoryListView' => 
  array (
    0 => '/dc/common/interfaces/RepositoryListView.php',
  ),
  'RepositoryUnitOfWorkInterface' => 
  array (
    0 => '/dc/common/interfaces/RepositoryUnitOfWorkInterface.php',
  ),
  'ServicePayload' => 
  array (
    0 => '/dc/common/interfaces/ServicePayload.php',
  ),
  'SessionFlashMessageBag' => 
  array (
    0 => '/dc/common/interfaces/SessionFlashMessageBag.php',
  ),
  'Template' => 
  array (
    0 => '/dc/common/interfaces/Template.php',
  ),
  'TemplateEngine' => 
  array (
    0 => '/dc/common/interfaces/TemplateEngine.php',
  ),
  'UnitOfWorkInterface' => 
  array (
    0 => '/dc/common/interfaces/UnitOfWorkInterface.php',
  ),
  'View' => 
  array (
    0 => '/dc/common/interfaces/View.php',
  ),
  'ViewModel' => 
  array (
    0 => '/dc/common/interfaces/ViewModel.php',
  ),
  'log_mysql' => 
  array (
    0 => '/dc/common/logparser/class.log.mysql.php',
  ),
  'log_output' => 
  array (
    0 => '/dc/common/logparser/class.log.output.php',
  ),
  'log' => 
  array (
    0 => '/dc/common/logparser/class.log.php',
  ),
  'log_processor' => 
  array (
    0 => '/dc/common/logparser/class.log.processor.php',
  ),
  'B' => 
  array (
    0 => '/dc/common/tests/HookTest.php',
  ),
  'C' => 
  array (
    0 => '/dc/common/tests/HookTest.php',
  ),
  'arrayGettableTrait' => 
  array (
    0 => '/dc/common/traits/arrayGettableTrait.php',
  ),
  'arrayMappableTrait' => 
  array (
    0 => '/dc/common/traits/arrayMappableTrait.php',
  ),
  'directoryFileWriterTrait' => 
  array (
    0 => '/dc/common/traits/directoryFileWriterTrait.php',
  ),
  'flattenedGettableTrait' => 
  array (
    0 => '/dc/common/traits/flattenedGettableTrait.php',
  ),
  'genericCollectionTrait' => 
  array (
    0 => '/dc/common/traits/genericCollectionTrait.php',
  ),
  'genericConfigTrait' => 
  array (
    0 => '/dc/common/traits/genericConfigTrait.php',
  ),
  'genericDBModelTrait' => 
  array (
    0 => '/dc/common/traits/genericDBModelTrait.php',
  ),
  'genericPlainTemplateTrait' => 
  array (
    0 => '/dc/common/traits/genericPlainTemplateTrait.php',
  ),
  'genericRepositoryListViewTrait' => 
  array (
    0 => '/dc/common/traits/genericRepositoryListViewTrait.php',
  ),
  'genericRepositoryTrait' => 
  array (
    0 => '/dc/common/traits/genericRepositoryTrait.php',
  ),
  'genericViewModelTrait' => 
  array (
    0 => '/dc/common/traits/genericViewModelTrait.php',
  ),
  'genericViewTrait' => 
  array (
    0 => '/dc/common/traits/genericViewTrait.php',
  ),
  'hasConfigTrait' => 
  array (
    0 => '/dc/common/traits/hasConfigTrait.php',
  ),
  'hookableTrait' => 
  array (
    0 => '/dc/common/traits/hookableTrait.php',
  ),
  'reflectionIDSetter' => 
  array (
    0 => '/dc/common/traits/reflectionIDSetter.php',
  ),
  'universallyGettableTrait' => 
  array (
    0 => '/dc/common/traits/universallyGettableTrait.php',
  ),
  'DcAutoloader' =>
  array (
    0 => '/dc/DcAutoloader.php',
  ),
  'PDORegionalizedTextProvider' => 
  array (
    0 => '/dc/regionalization/PDORegionalizedTextProvider.php',
  ),
  'PHPFileRegionalizedTextProvider' => 
  array (
    0 => '/dc/regionalization/PHPFileRegionalizedTextProvider.php',
  ),
  'RegionalizedTextProvider' => 
  array (
    0 => '/dc/regionalization/RegionalizedTextProvider.php',
  ),
);

    public function register()
    {
        spl_autoload_register([__NAMESPACE__ . '\\' . __CLASS__,'resolve']);
    }

    public static function resolve($class)
    {
        if (isset(static::$map[$class])) {
            foreach (static::$map[$class] as $includePath) {
                include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . $includePath);
            }
        }
    }

}