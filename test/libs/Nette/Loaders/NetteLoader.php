<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004, 2011 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Loaders
 */



/**
 * Nette auto loader is responsible for loading Nette classes and interfaces.
 *
 * @author     David Grudl
 */
class NetteLoader extends AutoLoader
{
	/** @var NetteLoader */
	private static $instance;

	/** @var array */
	public $list = array(
		'abortexception' => '/Application/exceptions/AbortException.php',
		'ambiguousserviceexception' => '/Injection/AmbiguousServiceException.php',
		'annotation' => '/Reflection/Annotation.php',
		'annotationsparser' => '/Reflection/AnnotationsParser.php',
		'appform' => '/Application/AppForm.php',
		'application' => '/Application/Application.php',
		'applicationexception' => '/Application/exceptions/ApplicationException.php',
		'argumentoutofrangeexception' => '/tools/exceptions.php',
		'arrayhash' => '/tools/ArrayHash.php',
		'arraylist' => '/tools/ArrayList.php',
		'arraytools' => '/tools/ArrayTools.php',
		'authenticationexception' => '/Security/AuthenticationException.php',
		'autoloader' => '/Loaders/AutoLoader.php',
		'badrequestexception' => '/Application/exceptions/BadRequestException.php',
		'badsignalexception' => '/Application/exceptions/BadSignalException.php',
		'button' => '/Forms/Controls/Button.php',
		'cache' => '/Caching/Cache.php',
		'cachinghelper' => '/Latte/CachingHelper.php',
		'callback' => '/tools/Callback.php',
		'callbackfilteriterator' => '/tools/iterators/CallbackFilterIterator.php',
		'checkbox' => '/Forms/Controls/Checkbox.php',
		'classreflection' => '/Reflection/ClassReflection.php',
		'clirouter' => '/Application/Routers/CliRouter.php',
		'component' => '/ComponentModel/Component.php',
		'componentcontainer' => '/ComponentModel/ComponentContainer.php',
		'config' => '/Config/Config.php',
		'configadapterini' => '/Config/ConfigAdapterIni.php',
		'configadapterneon' => '/Config/ConfigAdapterNeon.php',
		'configurator' => '/Environment/Configurator.php',
		'connection' => '/Database/Connection.php',
		'context' => '/Injection/Context.php',
		'control' => '/Application/Control.php',
		'databasepanel' => '/Database/Diagnostics/DatabasePanel.php',
		'databasereflection' => '/Database/Reflection/DatabaseReflection.php',
		'datetime53' => '/tools/DateTime53.php',
		'debug' => '/Diagnostics/Debug.php',
		'debughelpers' => '/Diagnostics/DebugHelpers.php',
		'debugpanel' => '/Diagnostics/DebugPanel.php',
		'defaultformrenderer' => '/Forms/Renderers/DefaultFormRenderer.php',
		'deprecatedexception' => '/tools/exceptions.php',
		'directorynotfoundexception' => '/tools/exceptions.php',
		'downloadresponse' => '/Application/Responses/DownloadResponse.php',
		'dummystorage' => '/Caching/DummyStorage.php',
		'environment' => '/Environment/Environment.php',
		'extensionreflection' => '/Reflection/ExtensionReflection.php',
		'fatalerrorexception' => '/tools/exceptions.php',
		'filejournal' => '/Caching/FileJournal.php',
		'filenotfoundexception' => '/tools/exceptions.php',
		'filestorage' => '/Caching/FileStorage.php',
		'filetemplate' => '/Templates/FileTemplate.php',
		'fileupload' => '/Forms/Controls/FileUpload.php',
		'finder' => '/tools/Finder.php',
		'forbiddenrequestexception' => '/Application/exceptions/ForbiddenRequestException.php',
		'form' => '/Forms/Form.php',
		'formcontainer' => '/Forms/FormContainer.php',
		'formcontrol' => '/Forms/Controls/FormControl.php',
		'formgroup' => '/Forms/FormGroup.php',
		'forwardingresponse' => '/Application/Responses/ForwardingResponse.php',
		'framework' => '/tools/Framework.php',
		'freezableobject' => '/tools/FreezableObject.php',
		'functionreflection' => '/Reflection/FunctionReflection.php',
		'genericrecursiveiterator' => '/tools/iterators/GenericRecursiveIterator.php',
		'groupedtableselection' => '/Database/Selector/GroupedTableSelection.php',
		'hiddenfield' => '/Forms/Controls/HiddenField.php',
		'html' => '/tools/Html.php',
		'httpcontext' => '/Http/HttpContext.php',
		'httprequest' => '/Http/HttpRequest.php',
		'httprequestfactory' => '/Http/HttpRequestFactory.php',
		'httpresponse' => '/Http/HttpResponse.php',
		'httpuploadedfile' => '/Http/HttpUploadedFile.php',
		'iannotation' => '/Reflection/IAnnotation.php',
		'iauthenticator' => '/Security/IAuthenticator.php',
		'iauthorizator' => '/Security/IAuthorizator.php',
		'icachejournal' => '/Caching/ICacheJournal.php',
		'icachestorage' => '/Caching/ICacheStorage.php',
		'icomponent' => '/ComponentModel/IComponent.php',
		'icomponentcontainer' => '/ComponentModel/IComponentContainer.php',
		'iconfigadapter' => '/Config/IConfigAdapter.php',
		'icontext' => '/Injection/IContext.php',
		'idebugpanel' => '/Diagnostics/IDebugPanel.php',
		'identity' => '/Security/Identity.php',
		'ifiletemplate' => '/Templates/IFileTemplate.php',
		'iformcontrol' => '/Forms/IFormControl.php',
		'iformrenderer' => '/Forms/IFormRenderer.php',
		'ifreezable' => '/tools/IFreezable.php',
		'ihttprequest' => '/Http/IHttpRequest.php',
		'ihttpresponse' => '/Http/IHttpResponse.php',
		'iidentity' => '/Security/IIdentity.php',
		'image' => '/tools/Image.php',
		'imagebutton' => '/Forms/Controls/ImageButton.php',
		'imailer' => '/Mail/IMailer.php',
		'instancefilteriterator' => '/tools/iterators/InstanceFilterIterator.php',
		'invalidlinkexception' => '/Application/exceptions/InvalidLinkException.php',
		'invalidpresenterexception' => '/Application/exceptions/InvalidPresenterException.php',
		'invalidstateexception' => '/tools/exceptions.php',
		'ioexception' => '/tools/exceptions.php',
		'ipartiallyrenderable' => '/Application/IPartiallyRenderable.php',
		'ipresenter' => '/Application/IPresenter.php',
		'ipresenterfactory' => '/Application/IPresenterFactory.php',
		'ipresenterresponse' => '/Application/IPresenterResponse.php',
		'irenderable' => '/Application/IRenderable.php',
		'iresource' => '/Security/IResource.php',
		'irole' => '/Security/IRole.php',
		'irouter' => '/Application/IRouter.php',
		'isessionstorage' => '/Http/ISessionStorage.php',
		'isignalreceiver' => '/Application/ISignalReceiver.php',
		'istatepersistent' => '/Application/IStatePersistent.php',
		'isubmittercontrol' => '/Forms/ISubmitterControl.php',
		'isupplementaldriver' => '/Database/ISupplementalDriver.php',
		'itemplate' => '/Templates/ITemplate.php',
		'itranslator' => '/Localization/ITranslator.php',
		'iuser' => '/Http/IUser.php',
		'json' => '/tools/Json.php',
		'jsonexception' => '/tools/JsonException.php',
		'jsonresponse' => '/Application/Responses/JsonResponse.php',
		'latteexception' => '/Latte/LatteException.php',
		'lattefilter' => '/Latte/LatteFilter.php',
		'lattemacros' => '/Latte/LatteMacros.php',
		'limitedscope' => '/Loaders/LimitedScope.php',
		'link' => '/Application/Link.php',
		'mail' => '/Mail/Mail.php',
		'mailmimepart' => '/Mail/MailMimePart.php',
		'mapiterator' => '/tools/iterators/MapIterator.php',
		'memberaccessexception' => '/tools/exceptions.php',
		'memcachedstorage' => '/Caching/MemcachedStorage.php',
		'memorystorage' => '/Caching/MemoryStorage.php',
		'methodreflection' => '/Reflection/MethodReflection.php',
		'multirouter' => '/Application/Routers/MultiRouter.php',
		'multiselectbox' => '/Forms/Controls/MultiSelectBox.php',
		'nclosurefix' => '/tools/Framework.php',
		'neon' => '/tools/Neon.php',
		'neonexception' => '/tools/Neon.php',
		'netteloader' => '/Loaders/NetteLoader.php',
		'notimplementedexception' => '/tools/exceptions.php',
		'notsupportedexception' => '/tools/exceptions.php',
		'object' => '/tools/Object.php',
		'objectmixin' => '/tools/ObjectMixin.php',
		'paginator' => '/tools/Paginator.php',
		'parameterreflection' => '/Reflection/ParameterReflection.php',
		'pdomssqldriver' => '/Database/Drivers/PdoMsSqlDriver.php',
		'pdomysqldriver' => '/Database/Drivers/PdoMySqlDriver.php',
		'pdoocidriver' => '/Database/Drivers/PdoOciDriver.php',
		'pdoodbcdriver' => '/Database/Drivers/PdoOdbcDriver.php',
		'pdopgsqldriver' => '/Database/Drivers/PdoPgSqlDriver.php',
		'pdosqlite2driver' => '/Database/Drivers/PdoSqlite2Driver.php',
		'pdosqlitedriver' => '/Database/Drivers/PdoSqliteDriver.php',
		'permission' => '/Security/Permission.php',
		'presenter' => '/Application/Presenter.php',
		'presentercomponent' => '/Application/PresenterComponent.php',
		'presentercomponentreflection' => '/Application/PresenterComponentReflection.php',
		'presenterfactory' => '/Application/PresenterFactory.php',
		'presenterrequest' => '/Application/PresenterRequest.php',
		'propertyreflection' => '/Reflection/PropertyReflection.php',
		'radiolist' => '/Forms/Controls/RadioList.php',
		'recursivecallbackfilteriterator' => '/tools/iterators/RecursiveCallbackFilterIterator.php',
		'recursivecomponentiterator' => '/ComponentModel/RecursiveComponentIterator.php',
		'redirectingresponse' => '/Application/Responses/RedirectingResponse.php',
		'regexpexception' => '/tools/RegexpException.php',
		'renderresponse' => '/Application/Responses/RenderResponse.php',
		'robotloader' => '/Loaders/RobotLoader.php',
		'route' => '/Application/Routers/Route.php',
		'routingdebugger' => '/Application/Diagnostics/RoutingDebugger.php',
		'row' => '/Database/Row.php',
		'rule' => '/Forms/Rule.php',
		'rules' => '/Forms/Rules.php',
		'safestream' => '/tools/SafeStream.php',
		'selectbox' => '/Forms/Controls/SelectBox.php',
		'sendmailmailer' => '/Mail/SendmailMailer.php',
		'session' => '/Http/Session.php',
		'sessionnamespace' => '/Http/SessionNamespace.php',
		'simpleauthenticator' => '/Security/SimpleAuthenticator.php',
		'simplerouter' => '/Application/Routers/SimpleRouter.php',
		'smartcachingiterator' => '/tools/iterators/SmartCachingIterator.php',
		'smtpexception' => '/Mail/SmtpException.php',
		'smtpmailer' => '/Mail/SmtpMailer.php',
		'sqlliteral' => '/Database/SqlLiteral.php',
		'sqlpreprocessor' => '/Database/SqlPreprocessor.php',
		'statement' => '/Database/Statement.php',
		'string' => '/tools/String.php',
		'submitbutton' => '/Forms/Controls/SubmitButton.php',
		'tablerow' => '/Database/Selector/TableRow.php',
		'tableselection' => '/Database/Selector/TableSelection.php',
		'template' => '/Templates/Template.php',
		'templatecachestorage' => '/Templates/TemplateCacheStorage.php',
		'templateexception' => '/Templates/TemplateException.php',
		'templatefilters' => '/Templates/TemplateFilters.php',
		'templatehelpers' => '/Templates/TemplateHelpers.php',
		'textarea' => '/Forms/Controls/TextArea.php',
		'textbase' => '/Forms/Controls/TextBase.php',
		'textinput' => '/Forms/Controls/TextInput.php',
		'tokenizer' => '/tools/Tokenizer.php',
		'tokenizerexception' => '/tools/TokenizerException.php',
		'tools' => '/tools/Tools.php',
		'uri' => '/Http/Uri.php',
		'uriscript' => '/Http/UriScript.php',
		'user' => '/Http/User.php',
	);



	/**
	 * Returns singleton instance with lazy instantiation.
	 * @return NetteLoader
	 */
	public static function getInstance()
	{
		if (self::$instance === NULL) {
			self::$instance = new self;
		}
		return self::$instance;
	}



	/**
	 * Handles autoloading of classes or interfaces.
	 * @param  string
	 * @return void
	 */
	public function tryLoad($type)
	{
		$type = ltrim(strtolower($type), '\\');
		if (isset($this->list[$type])) {
			LimitedScope::load(NETTE_DIR . $this->list[$type]);
			self::$count++;
		}
	}

}
