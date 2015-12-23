<?php
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InactiveScopeException;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
class appProdProjectContainer extends Container
{
    private $parameters;
    private $targetDirs = array();
    public function __construct()
    {
        $dir = __DIR__;
        for ($i = 1; $i <= 5; ++$i) {
            $this->targetDirs[$i] = $dir = dirname($dir);
        }
        $this->parameters = $this->getDefaultParameters();
        $this->services =
        $this->scopedServices =
        $this->scopeStacks = array();
        $this->scopes = array('request' => 'container');
        $this->scopeChildren = array('request' => array());
        $this->methodMap = array(
            'annotation_reader' => 'getAnnotationReaderService',
            'bazinga.oauth.controller.login' => 'getBazinga_Oauth_Controller_LoginService',
            'bazinga.oauth.controller.server' => 'getBazinga_Oauth_Controller_ServerService',
            'bazinga.oauth.entity_manager' => 'getBazinga_Oauth_EntityManagerService',
            'bazinga.oauth.event_listener.exception' => 'getBazinga_Oauth_EventListener_ExceptionService',
            'bazinga.oauth.event_listener.request' => 'getBazinga_Oauth_EventListener_RequestService',
            'bazinga.oauth.provider.token_provider' => 'getBazinga_Oauth_Provider_TokenProviderService',
            'bazinga.oauth.server_service' => 'getBazinga_Oauth_ServerServiceService',
            'bazinga.oauth.signature.hmac_sha1' => 'getBazinga_Oauth_Signature_HmacSha1Service',
            'bazinga.oauth.signature.plaintext' => 'getBazinga_Oauth_Signature_PlaintextService',
            'cache_clearer' => 'getCacheClearerService',
            'cache_warmer' => 'getCacheWarmerService',
            'controller_name_converter' => 'getControllerNameConverterService',
            'debug.emergency_logger_listener' => 'getDebug_EmergencyLoggerListenerService',
            'doctrine' => 'getDoctrineService',
            'doctrine.dbal.connection_factory' => 'getDoctrine_Dbal_ConnectionFactoryService',
            'doctrine.dbal.default_connection' => 'getDoctrine_Dbal_DefaultConnectionService',
            'doctrine.orm.default_entity_listener_resolver' => 'getDoctrine_Orm_DefaultEntityListenerResolverService',
            'doctrine.orm.default_entity_manager' => 'getDoctrine_Orm_DefaultEntityManagerService',
            'doctrine.orm.default_manager_configurator' => 'getDoctrine_Orm_DefaultManagerConfiguratorService',
            'doctrine.orm.validator.unique' => 'getDoctrine_Orm_Validator_UniqueService',
            'doctrine.orm.validator_initializer' => 'getDoctrine_Orm_ValidatorInitializerService',
            'doctrine_cache.providers.doctrine.orm.default_metadata_cache' => 'getDoctrineCache_Providers_Doctrine_Orm_DefaultMetadataCacheService',
            'doctrine_cache.providers.doctrine.orm.default_query_cache' => 'getDoctrineCache_Providers_Doctrine_Orm_DefaultQueryCacheService',
            'doctrine_cache.providers.doctrine.orm.default_result_cache' => 'getDoctrineCache_Providers_Doctrine_Orm_DefaultResultCacheService',
            'event_dispatcher' => 'getEventDispatcherService',
            'file_locator' => 'getFileLocatorService',
            'filesystem' => 'getFilesystemService',
            'form.csrf_provider' => 'getForm_CsrfProviderService',
            'form.factory' => 'getForm_FactoryService',
            'form.registry' => 'getForm_RegistryService',
            'form.resolved_type_factory' => 'getForm_ResolvedTypeFactoryService',
            'form.type.birthday' => 'getForm_Type_BirthdayService',
            'form.type.button' => 'getForm_Type_ButtonService',
            'form.type.checkbox' => 'getForm_Type_CheckboxService',
            'form.type.choice' => 'getForm_Type_ChoiceService',
            'form.type.collection' => 'getForm_Type_CollectionService',
            'form.type.country' => 'getForm_Type_CountryService',
            'form.type.currency' => 'getForm_Type_CurrencyService',
            'form.type.date' => 'getForm_Type_DateService',
            'form.type.datetime' => 'getForm_Type_DatetimeService',
            'form.type.email' => 'getForm_Type_EmailService',
            'form.type.entity' => 'getForm_Type_EntityService',
            'form.type.file' => 'getForm_Type_FileService',
            'form.type.form' => 'getForm_Type_FormService',
            'form.type.hidden' => 'getForm_Type_HiddenService',
            'form.type.integer' => 'getForm_Type_IntegerService',
            'form.type.language' => 'getForm_Type_LanguageService',
            'form.type.locale' => 'getForm_Type_LocaleService',
            'form.type.money' => 'getForm_Type_MoneyService',
            'form.type.number' => 'getForm_Type_NumberService',
            'form.type.password' => 'getForm_Type_PasswordService',
            'form.type.percent' => 'getForm_Type_PercentService',
            'form.type.radio' => 'getForm_Type_RadioService',
            'form.type.repeated' => 'getForm_Type_RepeatedService',
            'form.type.reset' => 'getForm_Type_ResetService',
            'form.type.search' => 'getForm_Type_SearchService',
            'form.type.submit' => 'getForm_Type_SubmitService',
            'form.type.text' => 'getForm_Type_TextService',
            'form.type.textarea' => 'getForm_Type_TextareaService',
            'form.type.time' => 'getForm_Type_TimeService',
            'form.type.timezone' => 'getForm_Type_TimezoneService',
            'form.type.url' => 'getForm_Type_UrlService',
            'form.type_extension.csrf' => 'getForm_TypeExtension_CsrfService',
            'form.type_extension.form.http_foundation' => 'getForm_TypeExtension_Form_HttpFoundationService',
            'form.type_extension.form.validator' => 'getForm_TypeExtension_Form_ValidatorService',
            'form.type_extension.repeated.validator' => 'getForm_TypeExtension_Repeated_ValidatorService',
            'form.type_extension.submit.validator' => 'getForm_TypeExtension_Submit_ValidatorService',
            'form.type_guesser.doctrine' => 'getForm_TypeGuesser_DoctrineService',
            'form.type_guesser.validator' => 'getForm_TypeGuesser_ValidatorService',
            'fos_oauth_server.access_token_manager.default' => 'getFosOauthServer_AccessTokenManager_DefaultService',
            'fos_oauth_server.auth_code_manager.default' => 'getFosOauthServer_AuthCodeManager_DefaultService',
            'fos_oauth_server.authorize.form' => 'getFosOauthServer_Authorize_FormService',
            'fos_oauth_server.authorize.form.handler.default' => 'getFosOauthServer_Authorize_Form_Handler_DefaultService',
            'fos_oauth_server.authorize.form.type' => 'getFosOauthServer_Authorize_Form_TypeService',
            'fos_oauth_server.client_manager.default' => 'getFosOauthServer_ClientManager_DefaultService',
            'fos_oauth_server.controller.token' => 'getFosOauthServer_Controller_TokenService',
            'fos_oauth_server.entity_manager' => 'getFosOauthServer_EntityManagerService',
            'fos_oauth_server.refresh_token_manager.default' => 'getFosOauthServer_RefreshTokenManager_DefaultService',
            'fos_oauth_server.server' => 'getFosOauthServer_ServerService',
            'fos_oauth_server.storage' => 'getFosOauthServer_StorageService',
            'fos_rest.body_listener' => 'getFosRest_BodyListenerService',
            'fos_rest.decoder.json' => 'getFosRest_Decoder_JsonService',
            'fos_rest.decoder.jsontoform' => 'getFosRest_Decoder_JsontoformService',
            'fos_rest.decoder.xml' => 'getFosRest_Decoder_XmlService',
            'fos_rest.decoder_provider' => 'getFosRest_DecoderProviderService',
            'fos_rest.form.extension.csrf_disable' => 'getFosRest_Form_Extension_CsrfDisableService',
            'fos_rest.format_negotiator' => 'getFosRest_FormatNegotiatorService',
            'fos_rest.inflector.doctrine' => 'getFosRest_Inflector_DoctrineService',
            'fos_rest.request.param_fetcher' => 'getFosRest_Request_ParamFetcherService',
            'fos_rest.request.param_fetcher.reader' => 'getFosRest_Request_ParamFetcher_ReaderService',
            'fos_rest.routing.loader.controller' => 'getFosRest_Routing_Loader_ControllerService',
            'fos_rest.routing.loader.processor' => 'getFosRest_Routing_Loader_ProcessorService',
            'fos_rest.routing.loader.reader.action' => 'getFosRest_Routing_Loader_Reader_ActionService',
            'fos_rest.routing.loader.reader.controller' => 'getFosRest_Routing_Loader_Reader_ControllerService',
            'fos_rest.routing.loader.xml_collection' => 'getFosRest_Routing_Loader_XmlCollectionService',
            'fos_rest.routing.loader.yaml_collection' => 'getFosRest_Routing_Loader_YamlCollectionService',
            'fos_rest.serializer' => 'getFosRest_SerializerService',
            'fos_rest.view.exception_wrapper_handler' => 'getFosRest_View_ExceptionWrapperHandlerService',
            'fos_rest.view_handler' => 'getFosRest_ViewHandlerService',
            'fragment.handler' => 'getFragment_HandlerService',
            'fragment.listener' => 'getFragment_ListenerService',
            'fragment.renderer.esi' => 'getFragment_Renderer_EsiService',
            'fragment.renderer.hinclude' => 'getFragment_Renderer_HincludeService',
            'fragment.renderer.inline' => 'getFragment_Renderer_InlineService',
            'http_kernel' => 'getHttpKernelService',
            'jms_serializer.array_collection_handler' => 'getJmsSerializer_ArrayCollectionHandlerService',
            'jms_serializer.constraint_violation_handler' => 'getJmsSerializer_ConstraintViolationHandlerService',
            'jms_serializer.datetime_handler' => 'getJmsSerializer_DatetimeHandlerService',
            'jms_serializer.doctrine_proxy_subscriber' => 'getJmsSerializer_DoctrineProxySubscriberService',
            'jms_serializer.form_error_handler' => 'getJmsSerializer_FormErrorHandlerService',
            'jms_serializer.handler_registry' => 'getJmsSerializer_HandlerRegistryService',
            'jms_serializer.json_deserialization_visitor' => 'getJmsSerializer_JsonDeserializationVisitorService',
            'jms_serializer.json_serialization_visitor' => 'getJmsSerializer_JsonSerializationVisitorService',
            'jms_serializer.metadata_driver' => 'getJmsSerializer_MetadataDriverService',
            'jms_serializer.naming_strategy' => 'getJmsSerializer_NamingStrategyService',
            'jms_serializer.object_constructor' => 'getJmsSerializer_ObjectConstructorService',
            'jms_serializer.php_collection_handler' => 'getJmsSerializer_PhpCollectionHandlerService',
            'jms_serializer.templating.helper.serializer' => 'getJmsSerializer_Templating_Helper_SerializerService',
            'jms_serializer.unserialize_object_constructor' => 'getJmsSerializer_UnserializeObjectConstructorService',
            'jms_serializer.xml_deserialization_visitor' => 'getJmsSerializer_XmlDeserializationVisitorService',
            'jms_serializer.xml_serialization_visitor' => 'getJmsSerializer_XmlSerializationVisitorService',
            'jms_serializer.yaml_serialization_visitor' => 'getJmsSerializer_YamlSerializationVisitorService',
            'kernel' => 'getKernelService',
            'knp_menu.factory' => 'getKnpMenu_FactoryService',
            'knp_menu.listener.voters' => 'getKnpMenu_Listener_VotersService',
            'knp_menu.matcher' => 'getKnpMenu_MatcherService',
            'knp_menu.menu_provider' => 'getKnpMenu_MenuProviderService',
            'knp_menu.renderer.list' => 'getKnpMenu_Renderer_ListService',
            'knp_menu.renderer_provider' => 'getKnpMenu_RendererProviderService',
            'knp_menu.templating.helper' => 'getKnpMenu_Templating_HelperService',
            'knp_menu.voter.router' => 'getKnpMenu_Voter_RouterService',
            'locale_listener' => 'getLocaleListenerService',
            'logger' => 'getLoggerService',
            'mautic.api.configbundle.subscriber' => 'getMautic_Api_Configbundle_SubscriberService',
            'mautic.api.oauth.event_listener' => 'getMautic_Api_Oauth_EventListenerService',
            'mautic.api.oauth1.nonce_provider' => 'getMautic_Api_Oauth1_NonceProviderService',
            'mautic.api.search.subscriber' => 'getMautic_Api_Search_SubscriberService',
            'mautic.api.subscriber' => 'getMautic_Api_SubscriberService',
            'mautic.asset.builder.subscriber' => 'getMautic_Asset_Builder_SubscriberService',
            'mautic.asset.campaignbundle.subscriber' => 'getMautic_Asset_Campaignbundle_SubscriberService',
            'mautic.asset.configbundle.subscriber' => 'getMautic_Asset_Configbundle_SubscriberService',
            'mautic.asset.emailbundle.subscriber' => 'getMautic_Asset_Emailbundle_SubscriberService',
            'mautic.asset.formbundle.subscriber' => 'getMautic_Asset_Formbundle_SubscriberService',
            'mautic.asset.leadbundle.subscriber' => 'getMautic_Asset_Leadbundle_SubscriberService',
            'mautic.asset.pagebundle.subscriber' => 'getMautic_Asset_Pagebundle_SubscriberService',
            'mautic.asset.pointbundle.subscriber' => 'getMautic_Asset_Pointbundle_SubscriberService',
            'mautic.asset.reportbundle.subscriber' => 'getMautic_Asset_Reportbundle_SubscriberService',
            'mautic.asset.search.subscriber' => 'getMautic_Asset_Search_SubscriberService',
            'mautic.asset.subscriber' => 'getMautic_Asset_SubscriberService',
            'mautic.asset.upload.error.handler' => 'getMautic_Asset_Upload_Error_HandlerService',
            'mautic.campaign.calendarbundle.subscriber' => 'getMautic_Campaign_Calendarbundle_SubscriberService',
            'mautic.campaign.leadbundle.subscriber' => 'getMautic_Campaign_Leadbundle_SubscriberService',
            'mautic.campaign.pointbundle.subscriber' => 'getMautic_Campaign_Pointbundle_SubscriberService',
            'mautic.campaign.search.subscriber' => 'getMautic_Campaign_Search_SubscriberService',
            'mautic.campaign.subscriber' => 'getMautic_Campaign_SubscriberService',
            'mautic.campaign.type.action.addremovelead' => 'getMautic_Campaign_Type_Action_AddremoveleadService',
            'mautic.campaign.type.campaignlist' => 'getMautic_Campaign_Type_CampaignlistService',
            'mautic.campaign.type.canvassettings' => 'getMautic_Campaign_Type_CanvassettingsService',
            'mautic.campaign.type.form' => 'getMautic_Campaign_Type_FormService',
            'mautic.campaign.type.leadsource' => 'getMautic_Campaign_Type_LeadsourceService',
            'mautic.campaign.type.trigger.leadchange' => 'getMautic_Campaign_Type_Trigger_LeadchangeService',
            'mautic.campaignrange.type.action' => 'getMautic_Campaignrange_Type_ActionService',
            'mautic.category.subscriber' => 'getMautic_Category_SubscriberService',
            'mautic.cloudstorage.remoteassetbrowse.subscriber' => 'getMautic_Cloudstorage_Remoteassetbrowse_SubscriberService',
            'mautic.config.subscriber' => 'getMautic_Config_SubscriberService',
            'mautic.configurator' => 'getMautic_ConfiguratorService',
            'mautic.core.auditlog.subscriber' => 'getMautic_Core_Auditlog_SubscriberService',
            'mautic.core.configbundle.subscriber' => 'getMautic_Core_Configbundle_SubscriberService',
            'mautic.core.errorhandler.subscriber' => 'getMautic_Core_Errorhandler_SubscriberService',
            'mautic.core.subscriber' => 'getMautic_Core_SubscriberService',
            'mautic.email.calendarbundle.subscriber' => 'getMautic_Email_Calendarbundle_SubscriberService',
            'mautic.email.campaignbundle.subscriber' => 'getMautic_Email_Campaignbundle_SubscriberService',
            'mautic.email.configbundle.subscriber' => 'getMautic_Email_Configbundle_SubscriberService',
            'mautic.email.formbundle.subscriber' => 'getMautic_Email_Formbundle_SubscriberService',
            'mautic.email.leadbundle.subscriber' => 'getMautic_Email_Leadbundle_SubscriberService',
            'mautic.email.pagebundle.subscriber' => 'getMautic_Email_Pagebundle_SubscriberService',
            'mautic.email.pointbundle.subscriber' => 'getMautic_Email_Pointbundle_SubscriberService',
            'mautic.email.reportbundle.subscriber' => 'getMautic_Email_Reportbundle_SubscriberService',
            'mautic.email.search.subscriber' => 'getMautic_Email_Search_SubscriberService',
            'mautic.email.subscriber' => 'getMautic_Email_SubscriberService',
            'mautic.email.type.batch_send' => 'getMautic_Email_Type_BatchSendService',
            'mautic.email.type.email_abtest_settings' => 'getMautic_Email_Type_EmailAbtestSettingsService',
            'mautic.email.webhook.subscriber' => 'getMautic_Email_Webhook_SubscriberService',
            'mautic.emailbuilder.subscriber' => 'getMautic_Emailbuilder_SubscriberService',
            'mautic.exception.listener' => 'getMautic_Exception_ListenerService',
            'mautic.factory' => 'getMautic_FactoryService',
            'mautic.form.calendarbundle.subscriber' => 'getMautic_Form_Calendarbundle_SubscriberService',
            'mautic.form.campaignbundle.subscriber' => 'getMautic_Form_Campaignbundle_SubscriberService',
            'mautic.form.emailbundle.subscriber' => 'getMautic_Form_Emailbundle_SubscriberService',
            'mautic.form.leadbundle.subscriber' => 'getMautic_Form_Leadbundle_SubscriberService',
            'mautic.form.pagebundle.subscriber' => 'getMautic_Form_Pagebundle_SubscriberService',
            'mautic.form.pointbundle.subscriber' => 'getMautic_Form_Pointbundle_SubscriberService',
            'mautic.form.reportbundle.subscriber' => 'getMautic_Form_Reportbundle_SubscriberService',
            'mautic.form.search.subscriber' => 'getMautic_Form_Search_SubscriberService',
            'mautic.form.subscriber' => 'getMautic_Form_SubscriberService',
            'mautic.form.type.action' => 'getMautic_Form_Type_ActionService',
            'mautic.form.type.apiclients' => 'getMautic_Form_Type_ApiclientsService',
            'mautic.form.type.apiconfig' => 'getMautic_Form_Type_ApiconfigService',
            'mautic.form.type.asset' => 'getMautic_Form_Type_AssetService',
            'mautic.form.type.assetconfig' => 'getMautic_Form_Type_AssetconfigService',
            'mautic.form.type.assetlist' => 'getMautic_Form_Type_AssetlistService',
            'mautic.form.type.button_group' => 'getMautic_Form_Type_ButtonGroupService',
            'mautic.form.type.campaignevent_assetdownload' => 'getMautic_Form_Type_CampaigneventAssetdownloadService',
            'mautic.form.type.campaignevent_form_field_value' => 'getMautic_Form_Type_CampaigneventFormFieldValueService',
            'mautic.form.type.campaignevent_formsubmit' => 'getMautic_Form_Type_CampaigneventFormsubmitService',
            'mautic.form.type.campaignevent_lead_field_value' => 'getMautic_Form_Type_CampaigneventLeadFieldValueService',
            'mautic.form.type.category' => 'getMautic_Form_Type_CategoryService',
            'mautic.form.type.category_bundles_form' => 'getMautic_Form_Type_CategoryBundlesFormService',
            'mautic.form.type.category_form' => 'getMautic_Form_Type_CategoryFormService',
            'mautic.form.type.cloudstorage.openstack' => 'getMautic_Form_Type_Cloudstorage_OpenstackService',
            'mautic.form.type.cloudstorage.rackspace' => 'getMautic_Form_Type_Cloudstorage_RackspaceService',
            'mautic.form.type.config' => 'getMautic_Form_Type_ConfigService',
            'mautic.form.type.coreconfig' => 'getMautic_Form_Type_CoreconfigService',
            'mautic.form.type.coreconfig_monitored_email' => 'getMautic_Form_Type_CoreconfigMonitoredEmailService',
            'mautic.form.type.coreconfig_monitored_mailboxes' => 'getMautic_Form_Type_CoreconfigMonitoredMailboxesService',
            'mautic.form.type.dynamiclist' => 'getMautic_Form_Type_DynamiclistService',
            'mautic.form.type.email' => 'getMautic_Form_Type_EmailService',
            'mautic.form.type.email_list' => 'getMautic_Form_Type_EmailListService',
            'mautic.form.type.emailconfig' => 'getMautic_Form_Type_EmailconfigService',
            'mautic.form.type.emailmarketing.constantcontact' => 'getMautic_Form_Type_Emailmarketing_ConstantcontactService',
            'mautic.form.type.emailmarketing.icontact' => 'getMautic_Form_Type_Emailmarketing_IcontactService',
            'mautic.form.type.emailmarketing.mailchimp' => 'getMautic_Form_Type_Emailmarketing_MailchimpService',
            'mautic.form.type.emailopen_list' => 'getMautic_Form_Type_EmailopenListService',
            'mautic.form.type.emailsend_list' => 'getMautic_Form_Type_EmailsendListService',
            'mautic.form.type.emailvariant' => 'getMautic_Form_Type_EmailvariantService',
            'mautic.form.type.field' => 'getMautic_Form_Type_FieldService',
            'mautic.form.type.field_propertycaptcha' => 'getMautic_Form_Type_FieldPropertycaptchaService',
            'mautic.form.type.field_propertygroup' => 'getMautic_Form_Type_FieldPropertygroupService',
            'mautic.form.type.field_propertyplaceholder' => 'getMautic_Form_Type_FieldPropertyplaceholderService',
            'mautic.form.type.field_propertyselect' => 'getMautic_Form_Type_FieldPropertyselectService',
            'mautic.form.type.field_propertytext' => 'getMautic_Form_Type_FieldPropertytextService',
            'mautic.form.type.filter_selector' => 'getMautic_Form_Type_FilterSelectorService',
            'mautic.form.type.form' => 'getMautic_Form_Type_FormService',
            'mautic.form.type.form_buttons' => 'getMautic_Form_Type_FormButtonsService',
            'mautic.form.type.form_list' => 'getMautic_Form_Type_FormListService',
            'mautic.form.type.form_submitaction_sendemail' => 'getMautic_Form_Type_FormSubmitactionSendemailService',
            'mautic.form.type.formsubmit_assetdownload' => 'getMautic_Form_Type_FormsubmitAssetdownloadService',
            'mautic.form.type.formsubmit_sendemail_admin' => 'getMautic_Form_Type_FormsubmitSendemailAdminService',
            'mautic.form.type.hidden_entity' => 'getMautic_Form_Type_HiddenEntityService',
            'mautic.form.type.integration.config' => 'getMautic_Form_Type_Integration_ConfigService',
            'mautic.form.type.integration.details' => 'getMautic_Form_Type_Integration_DetailsService',
            'mautic.form.type.integration.fields' => 'getMautic_Form_Type_Integration_FieldsService',
            'mautic.form.type.integration.keys' => 'getMautic_Form_Type_Integration_KeysService',
            'mautic.form.type.integration.list' => 'getMautic_Form_Type_Integration_ListService',
            'mautic.form.type.integration.settings' => 'getMautic_Form_Type_Integration_SettingsService',
            'mautic.form.type.lead' => 'getMautic_Form_Type_LeadService',
            'mautic.form.type.lead.submitaction.changelist' => 'getMautic_Form_Type_Lead_Submitaction_ChangelistService',
            'mautic.form.type.lead.submitaction.pointschange' => 'getMautic_Form_Type_Lead_Submitaction_PointschangeService',
            'mautic.form.type.lead_batch' => 'getMautic_Form_Type_LeadBatchService',
            'mautic.form.type.lead_batch_dnc' => 'getMautic_Form_Type_LeadBatchDncService',
            'mautic.form.type.lead_field_import' => 'getMautic_Form_Type_LeadFieldImportService',
            'mautic.form.type.lead_fields' => 'getMautic_Form_Type_LeadFieldsService',
            'mautic.form.type.lead_import' => 'getMautic_Form_Type_LeadImportService',
            'mautic.form.type.lead_merge' => 'getMautic_Form_Type_LeadMergeService',
            'mautic.form.type.lead_quickemail' => 'getMautic_Form_Type_LeadQuickemailService',
            'mautic.form.type.lead_tag' => 'getMautic_Form_Type_LeadTagService',
            'mautic.form.type.lead_tags' => 'getMautic_Form_Type_LeadTagsService',
            'mautic.form.type.leadfield' => 'getMautic_Form_Type_LeadfieldService',
            'mautic.form.type.leadlist' => 'getMautic_Form_Type_LeadlistService',
            'mautic.form.type.leadlist_action' => 'getMautic_Form_Type_LeadlistActionService',
            'mautic.form.type.leadlist_choices' => 'getMautic_Form_Type_LeadlistChoicesService',
            'mautic.form.type.leadlist_filter' => 'getMautic_Form_Type_LeadlistFilterService',
            'mautic.form.type.leadlist_trigger' => 'getMautic_Form_Type_LeadlistTriggerService',
            'mautic.form.type.leadnote' => 'getMautic_Form_Type_LeadnoteService',
            'mautic.form.type.leadpoints_action' => 'getMautic_Form_Type_LeadpointsActionService',
            'mautic.form.type.leadpoints_trigger' => 'getMautic_Form_Type_LeadpointsTriggerService',
            'mautic.form.type.modify_lead_tags' => 'getMautic_Form_Type_ModifyLeadTagsService',
            'mautic.form.type.page' => 'getMautic_Form_Type_PageService',
            'mautic.form.type.page_abtest_settings' => 'getMautic_Form_Type_PageAbtestSettingsService',
            'mautic.form.type.page_publish_dates' => 'getMautic_Form_Type_PagePublishDatesService',
            'mautic.form.type.pageconfig' => 'getMautic_Form_Type_PageconfigService',
            'mautic.form.type.pagehit.campaign_trigger' => 'getMautic_Form_Type_Pagehit_CampaignTriggerService',
            'mautic.form.type.pagelist' => 'getMautic_Form_Type_PagelistService',
            'mautic.form.type.pagevariant' => 'getMautic_Form_Type_PagevariantService',
            'mautic.form.type.passwordreset' => 'getMautic_Form_Type_PasswordresetService',
            'mautic.form.type.permissionlist' => 'getMautic_Form_Type_PermissionlistService',
            'mautic.form.type.permissions' => 'getMautic_Form_Type_PermissionsService',
            'mautic.form.type.pointaction_assetdownload' => 'getMautic_Form_Type_PointactionAssetdownloadService',
            'mautic.form.type.pointaction_formsubmit' => 'getMautic_Form_Type_PointactionFormsubmitService',
            'mautic.form.type.pointaction_pointhit' => 'getMautic_Form_Type_PointactionPointhitService',
            'mautic.form.type.pointaction_urlhit' => 'getMautic_Form_Type_PointactionUrlhitService',
            'mautic.form.type.redirect_list' => 'getMautic_Form_Type_RedirectListService',
            'mautic.form.type.report' => 'getMautic_Form_Type_ReportService',
            'mautic.form.type.report_filters' => 'getMautic_Form_Type_ReportFiltersService',
            'mautic.form.type.role' => 'getMautic_Form_Type_RoleService',
            'mautic.form.type.slideshow_config' => 'getMautic_Form_Type_SlideshowConfigService',
            'mautic.form.type.slideshow_slide_config' => 'getMautic_Form_Type_SlideshowSlideConfigService',
            'mautic.form.type.social.facebook' => 'getMautic_Form_Type_Social_FacebookService',
            'mautic.form.type.social.googleplus' => 'getMautic_Form_Type_Social_GoogleplusService',
            'mautic.form.type.social.linkedin' => 'getMautic_Form_Type_Social_LinkedinService',
            'mautic.form.type.social.twitter' => 'getMautic_Form_Type_Social_TwitterService',
            'mautic.form.type.sortablelist' => 'getMautic_Form_Type_SortablelistService',
            'mautic.form.type.spacer' => 'getMautic_Form_Type_SpacerService',
            'mautic.form.type.standalone_button' => 'getMautic_Form_Type_StandaloneButtonService',
            'mautic.form.type.table_order' => 'getMautic_Form_Type_TableOrderService',
            'mautic.form.type.tel' => 'getMautic_Form_Type_TelService',
            'mautic.form.type.theme_list' => 'getMautic_Form_Type_ThemeListService',
            'mautic.form.type.updatelead_action' => 'getMautic_Form_Type_UpdateleadActionService',
            'mautic.form.type.user' => 'getMautic_Form_Type_UserService',
            'mautic.form.type.user_list' => 'getMautic_Form_Type_UserListService',
            'mautic.form.type.webhook' => 'getMautic_Form_Type_WebhookService',
            'mautic.form.type.webhookconfig' => 'getMautic_Form_Type_WebhookconfigService',
            'mautic.form.type.yesno_button_group' => 'getMautic_Form_Type_YesnoButtonGroupService',
            'mautic.form.webhook.subscriber' => 'getMautic_Form_Webhook_SubscriberService',
            'mautic.helper.assetgeneration' => 'getMautic_Helper_AssetgenerationService',
            'mautic.helper.cache' => 'getMautic_Helper_CacheService',
            'mautic.helper.cookie' => 'getMautic_Helper_CookieService',
            'mautic.helper.encryption' => 'getMautic_Helper_EncryptionService',
            'mautic.helper.integration' => 'getMautic_Helper_IntegrationService',
            'mautic.helper.language' => 'getMautic_Helper_LanguageService',
            'mautic.helper.mailbox' => 'getMautic_Helper_MailboxService',
            'mautic.helper.menu' => 'getMautic_Helper_MenuService',
            'mautic.helper.message' => 'getMautic_Helper_MessageService',
            'mautic.helper.template.analytics' => 'getMautic_Helper_Template_AnalyticsService',
            'mautic.helper.template.avatar' => 'getMautic_Helper_Template_AvatarService',
            'mautic.helper.template.button' => 'getMautic_Helper_Template_ButtonService',
            'mautic.helper.template.canvas' => 'getMautic_Helper_Template_CanvasService',
            'mautic.helper.template.date' => 'getMautic_Helper_Template_DateService',
            'mautic.helper.template.exception' => 'getMautic_Helper_Template_ExceptionService',
            'mautic.helper.template.formatter' => 'getMautic_Helper_Template_FormatterService',
            'mautic.helper.template.gravatar' => 'getMautic_Helper_Template_GravatarService',
            'mautic.helper.template.mautibot' => 'getMautic_Helper_Template_MautibotService',
            'mautic.helper.template.security' => 'getMautic_Helper_Template_SecurityService',
            'mautic.helper.theme' => 'getMautic_Helper_ThemeService',
            'mautic.helper.update' => 'getMautic_Helper_UpdateService',
            'mautic.lead.calendarbundle.subscriber' => 'getMautic_Lead_Calendarbundle_SubscriberService',
            'mautic.lead.campaignbundle.subscriber' => 'getMautic_Lead_Campaignbundle_SubscriberService',
            'mautic.lead.constraint.alias' => 'getMautic_Lead_Constraint_AliasService',
            'mautic.lead.doctrine.subscriber' => 'getMautic_Lead_Doctrine_SubscriberService',
            'mautic.lead.emailbundle.subscriber' => 'getMautic_Lead_Emailbundle_SubscriberService',
            'mautic.lead.formbundle.subscriber' => 'getMautic_Lead_Formbundle_SubscriberService',
            'mautic.lead.pointbundle.subscriber' => 'getMautic_Lead_Pointbundle_SubscriberService',
            'mautic.lead.reportbundle.subscriber' => 'getMautic_Lead_Reportbundle_SubscriberService',
            'mautic.lead.search.subscriber' => 'getMautic_Lead_Search_SubscriberService',
            'mautic.lead.subscriber' => 'getMautic_Lead_SubscriberService',
            'mautic.menu.admin' => 'getMautic_Menu_AdminService',
            'mautic.menu.builder' => 'getMautic_Menu_BuilderService',
            'mautic.menu.main' => 'getMautic_Menu_MainService',
            'mautic.menu_renderer' => 'getMautic_MenuRendererService',
            'mautic.page.calendarbundle.subscriber' => 'getMautic_Page_Calendarbundle_SubscriberService',
            'mautic.page.campaignbundle.subscriber' => 'getMautic_Page_Campaignbundle_SubscriberService',
            'mautic.page.configbundle.subscriber' => 'getMautic_Page_Configbundle_SubscriberService',
            'mautic.page.leadbundle.subscriber' => 'getMautic_Page_Leadbundle_SubscriberService',
            'mautic.page.pointbundle.subscriber' => 'getMautic_Page_Pointbundle_SubscriberService',
            'mautic.page.reportbundle.subscriber' => 'getMautic_Page_Reportbundle_SubscriberService',
            'mautic.page.search.subscriber' => 'getMautic_Page_Search_SubscriberService',
            'mautic.page.subscriber' => 'getMautic_Page_SubscriberService',
            'mautic.page.webhook.subscriber' => 'getMautic_Page_Webhook_SubscriberService',
            'mautic.pagebuilder.subscriber' => 'getMautic_Pagebuilder_SubscriberService',
            'mautic.permission.manager' => 'getMautic_Permission_ManagerService',
            'mautic.permission.repository' => 'getMautic_Permission_RepositoryService',
            'mautic.plugin.campaignbundle.subscriber' => 'getMautic_Plugin_Campaignbundle_SubscriberService',
            'mautic.plugin.formbundle.subscriber' => 'getMautic_Plugin_Formbundle_SubscriberService',
            'mautic.plugin.pointbundle.subscriber' => 'getMautic_Plugin_Pointbundle_SubscriberService',
            'mautic.point.leadbundle.subscriber' => 'getMautic_Point_Leadbundle_SubscriberService',
            'mautic.point.search.subscriber' => 'getMautic_Point_Search_SubscriberService',
            'mautic.point.subscriber' => 'getMautic_Point_SubscriberService',
            'mautic.point.type.action' => 'getMautic_Point_Type_ActionService',
            'mautic.point.type.form' => 'getMautic_Point_Type_FormService',
            'mautic.point.type.genericpoint_settings' => 'getMautic_Point_Type_GenericpointSettingsService',
            'mautic.pointtrigger.type.action' => 'getMautic_Pointtrigger_Type_ActionService',
            'mautic.pointtrigger.type.form' => 'getMautic_Pointtrigger_Type_FormService',
            'mautic.report.report.subscriber' => 'getMautic_Report_Report_SubscriberService',
            'mautic.report.search.subscriber' => 'getMautic_Report_Search_SubscriberService',
            'mautic.route_loader' => 'getMautic_RouteLoaderService',
            'mautic.security' => 'getMautic_SecurityService',
            'mautic.security.authentication_handler' => 'getMautic_Security_AuthenticationHandlerService',
            'mautic.security.logout_handler' => 'getMautic_Security_LogoutHandlerService',
            'mautic.tblprefix_subscriber' => 'getMautic_TblprefixSubscriberService',
            'mautic.templating.name_parser' => 'getMautic_Templating_NameParserService',
            'mautic.translation.loader' => 'getMautic_Translation_LoaderService',
            'mautic.transport.amazon' => 'getMautic_Transport_AmazonService',
            'mautic.transport.mandrill' => 'getMautic_Transport_MandrillService',
            'mautic.transport.postmark' => 'getMautic_Transport_PostmarkService',
            'mautic.transport.sendgrid' => 'getMautic_Transport_SendgridService',
            'mautic.user.manager' => 'getMautic_User_ManagerService',
            'mautic.user.provider' => 'getMautic_User_ProviderService',
            'mautic.user.repository' => 'getMautic_User_RepositoryService',
            'mautic.user.search.subscriber' => 'getMautic_User_Search_SubscriberService',
            'mautic.user.subscriber' => 'getMautic_User_SubscriberService',
            'mautic.validator.leadlistaccess' => 'getMautic_Validator_LeadlistaccessService',
            'mautic.validator.oauthcallback' => 'getMautic_Validator_OauthcallbackService',
            'mautic.webhook.audit.subscriber' => 'getMautic_Webhook_Audit_SubscriberService',
            'mautic.webhook.config.subscriber' => 'getMautic_Webhook_Config_SubscriberService',
            'mautic.webhook.email.subscriber' => 'getMautic_Webhook_Email_SubscriberService',
            'mautic.webhook.form.subscriber' => 'getMautic_Webhook_Form_SubscriberService',
            'mautic.webhook.lead.subscriber' => 'getMautic_Webhook_Lead_SubscriberService',
            'mautic.webhook.page.hit.subscriber' => 'getMautic_Webhook_Page_Hit_SubscriberService',
            'mautic.webhook.subscriber' => 'getMautic_Webhook_SubscriberService',
            'monolog.handler.main' => 'getMonolog_Handler_MainService',
            'monolog.handler.mautic' => 'getMonolog_Handler_MauticService',
            'monolog.handler.nested' => 'getMonolog_Handler_NestedService',
            'monolog.logger.doctrine' => 'getMonolog_Logger_DoctrineService',
            'monolog.logger.emergency' => 'getMonolog_Logger_EmergencyService',
            'monolog.logger.mautic' => 'getMonolog_Logger_MauticService',
            'monolog.logger.request' => 'getMonolog_Logger_RequestService',
            'monolog.logger.router' => 'getMonolog_Logger_RouterService',
            'monolog.logger.security' => 'getMonolog_Logger_SecurityService',
            'oneup_uploader.chunk_manager' => 'getOneupUploader_ChunkManagerService',
            'oneup_uploader.chunks_storage' => 'getOneupUploader_ChunksStorageService',
            'oneup_uploader.controller.mautic' => 'getOneupUploader_Controller_MauticService',
            'oneup_uploader.namer.uniqid' => 'getOneupUploader_Namer_UniqidService',
            'oneup_uploader.orphanage_manager' => 'getOneupUploader_OrphanageManagerService',
            'oneup_uploader.pre_upload' => 'getOneupUploader_PreUploadService',
            'oneup_uploader.routing.loader' => 'getOneupUploader_Routing_LoaderService',
            'oneup_uploader.storage.asset' => 'getOneupUploader_Storage_AssetService',
            'oneup_uploader.templating.uploader_helper' => 'getOneupUploader_Templating_UploaderHelperService',
            'oneup_uploader.twig.extension.uploader' => 'getOneupUploader_Twig_Extension_UploaderService',
            'oneup_uploader.validation_listener.allowed_mimetype' => 'getOneupUploader_ValidationListener_AllowedMimetypeService',
            'oneup_uploader.validation_listener.disallowed_mimetype' => 'getOneupUploader_ValidationListener_DisallowedMimetypeService',
            'oneup_uploader.validation_listener.max_size' => 'getOneupUploader_ValidationListener_MaxSizeService',
            'property_accessor' => 'getPropertyAccessorService',
            'request' => 'getRequestService',
            'request_stack' => 'getRequestStackService',
            'response_listener' => 'getResponseListenerService',
            'router' => 'getRouterService',
            'router.request_context' => 'getRouter_RequestContextService',
            'router_listener' => 'getRouterListenerService',
            'routing.loader' => 'getRouting_LoaderService',
            'security.access.decision_manager' => 'getSecurity_Access_DecisionManagerService',
            'security.access_listener' => 'getSecurity_AccessListenerService',
            'security.access_map' => 'getSecurity_AccessMapService',
            'security.authentication.manager' => 'getSecurity_Authentication_ManagerService',
            'security.authentication.session_strategy' => 'getSecurity_Authentication_SessionStrategyService',
            'security.authentication.trust_resolver' => 'getSecurity_Authentication_TrustResolverService',
            'security.authentication_utils' => 'getSecurity_AuthenticationUtilsService',
            'security.authorization_checker' => 'getSecurity_AuthorizationCheckerService',
            'security.channel_listener' => 'getSecurity_ChannelListenerService',
            'security.context' => 'getSecurity_ContextService',
            'security.context_listener.1' => 'getSecurity_ContextListener_1Service',
            'security.csrf.token_manager' => 'getSecurity_Csrf_TokenManagerService',
            'security.encoder_factory' => 'getSecurity_EncoderFactoryService',
            'security.firewall' => 'getSecurity_FirewallService',
            'security.firewall.map.context.api' => 'getSecurity_Firewall_Map_Context_ApiService',
            'security.firewall.map.context.dev' => 'getSecurity_Firewall_Map_Context_DevService',
            'security.firewall.map.context.install' => 'getSecurity_Firewall_Map_Context_InstallService',
            'security.firewall.map.context.login' => 'getSecurity_Firewall_Map_Context_LoginService',
            'security.firewall.map.context.main' => 'getSecurity_Firewall_Map_Context_MainService',
            'security.firewall.map.context.oauth1_access_token' => 'getSecurity_Firewall_Map_Context_Oauth1AccessTokenService',
            'security.firewall.map.context.oauth1_area' => 'getSecurity_Firewall_Map_Context_Oauth1AreaService',
            'security.firewall.map.context.oauth1_request_token' => 'getSecurity_Firewall_Map_Context_Oauth1RequestTokenService',
            'security.firewall.map.context.oauth2_area' => 'getSecurity_Firewall_Map_Context_Oauth2AreaService',
            'security.firewall.map.context.oauth2_token' => 'getSecurity_Firewall_Map_Context_Oauth2TokenService',
            'security.firewall.map.context.public' => 'getSecurity_Firewall_Map_Context_PublicService',
            'security.http_utils' => 'getSecurity_HttpUtilsService',
            'security.password_encoder' => 'getSecurity_PasswordEncoderService',
            'security.rememberme.response_listener' => 'getSecurity_Rememberme_ResponseListenerService',
            'security.secure_random' => 'getSecurity_SecureRandomService',
            'security.token_storage' => 'getSecurity_TokenStorageService',
            'security.validator.user_password' => 'getSecurity_Validator_UserPasswordService',
            'service_container' => 'getServiceContainerService',
            'session' => 'getSessionService',
            'session.save_listener' => 'getSession_SaveListenerService',
            'session.storage.filesystem' => 'getSession_Storage_FilesystemService',
            'session.storage.metadata_bag' => 'getSession_Storage_MetadataBagService',
            'session.storage.native' => 'getSession_Storage_NativeService',
            'session.storage.php_bridge' => 'getSession_Storage_PhpBridgeService',
            'session_listener' => 'getSessionListenerService',
            'streamed_response_listener' => 'getStreamedResponseListenerService',
            'swiftmailer.email_sender.listener' => 'getSwiftmailer_EmailSender_ListenerService',
            'swiftmailer.mailer.default' => 'getSwiftmailer_Mailer_DefaultService',
            'swiftmailer.mailer.default.transport' => 'getSwiftmailer_Mailer_Default_TransportService',
            'templating' => 'getTemplatingService',
            'templating.asset.package_factory' => 'getTemplating_Asset_PackageFactoryService',
            'templating.filename_parser' => 'getTemplating_FilenameParserService',
            'templating.globals' => 'getTemplating_GlobalsService',
            'templating.helper.actions' => 'getTemplating_Helper_ActionsService',
            'templating.helper.assets' => 'getTemplating_Helper_AssetsService',
            'templating.helper.code' => 'getTemplating_Helper_CodeService',
            'templating.helper.form' => 'getTemplating_Helper_FormService',
            'templating.helper.logout_url' => 'getTemplating_Helper_LogoutUrlService',
            'templating.helper.request' => 'getTemplating_Helper_RequestService',
            'templating.helper.router' => 'getTemplating_Helper_RouterService',
            'templating.helper.security' => 'getTemplating_Helper_SecurityService',
            'templating.helper.session' => 'getTemplating_Helper_SessionService',
            'templating.helper.slots' => 'getTemplating_Helper_SlotsService',
            'templating.helper.stopwatch' => 'getTemplating_Helper_StopwatchService',
            'templating.helper.translator' => 'getTemplating_Helper_TranslatorService',
            'templating.loader' => 'getTemplating_LoaderService',
            'templating.locator' => 'getTemplating_LocatorService',
            'templating.name_parser' => 'getTemplating_NameParserService',
            'transifex' => 'getTransifexService',
            'translation.dumper.csv' => 'getTranslation_Dumper_CsvService',
            'translation.dumper.ini' => 'getTranslation_Dumper_IniService',
            'translation.dumper.json' => 'getTranslation_Dumper_JsonService',
            'translation.dumper.mo' => 'getTranslation_Dumper_MoService',
            'translation.dumper.php' => 'getTranslation_Dumper_PhpService',
            'translation.dumper.po' => 'getTranslation_Dumper_PoService',
            'translation.dumper.qt' => 'getTranslation_Dumper_QtService',
            'translation.dumper.res' => 'getTranslation_Dumper_ResService',
            'translation.dumper.xliff' => 'getTranslation_Dumper_XliffService',
            'translation.dumper.yml' => 'getTranslation_Dumper_YmlService',
            'translation.extractor' => 'getTranslation_ExtractorService',
            'translation.extractor.php' => 'getTranslation_Extractor_PhpService',
            'translation.loader' => 'getTranslation_LoaderService',
            'translation.loader.csv' => 'getTranslation_Loader_CsvService',
            'translation.loader.dat' => 'getTranslation_Loader_DatService',
            'translation.loader.ini' => 'getTranslation_Loader_IniService',
            'translation.loader.json' => 'getTranslation_Loader_JsonService',
            'translation.loader.mo' => 'getTranslation_Loader_MoService',
            'translation.loader.php' => 'getTranslation_Loader_PhpService',
            'translation.loader.po' => 'getTranslation_Loader_PoService',
            'translation.loader.qt' => 'getTranslation_Loader_QtService',
            'translation.loader.res' => 'getTranslation_Loader_ResService',
            'translation.loader.xliff' => 'getTranslation_Loader_XliffService',
            'translation.loader.yml' => 'getTranslation_Loader_YmlService',
            'translation.writer' => 'getTranslation_WriterService',
            'translator.default' => 'getTranslator_DefaultService',
            'uri_signer' => 'getUriSignerService',
            'validator' => 'getValidatorService',
            'validator.builder' => 'getValidator_BuilderService',
            'validator.email' => 'getValidator_EmailService',
            'validator.expression' => 'getValidator_ExpressionService',
        );
        $this->aliases = array(
            'database_connection' => 'doctrine.dbal.default_connection',
            'doctrine.orm.default_metadata_cache' => 'doctrine_cache.providers.doctrine.orm.default_metadata_cache',
            'doctrine.orm.default_query_cache' => 'doctrine_cache.providers.doctrine.orm.default_query_cache',
            'doctrine.orm.default_result_cache' => 'doctrine_cache.providers.doctrine.orm.default_result_cache',
            'doctrine.orm.entity_manager' => 'doctrine.orm.default_entity_manager',
            'fos_oauth_server.access_token_manager' => 'fos_oauth_server.access_token_manager.default',
            'fos_oauth_server.auth_code_manager' => 'fos_oauth_server.auth_code_manager.default',
            'fos_oauth_server.authorize.form.handler' => 'fos_oauth_server.authorize.form.handler.default',
            'fos_oauth_server.client_manager' => 'fos_oauth_server.client_manager.default',
            'fos_oauth_server.refresh_token_manager' => 'fos_oauth_server.refresh_token_manager.default',
            'fos_rest.inflector' => 'fos_rest.inflector.doctrine',
            'fos_rest.router' => 'router',
            'fos_rest.templating' => 'templating',
            'jms_serializer' => 'fos_rest.serializer',
            'mailer' => 'swiftmailer.mailer.default',
            'serializer' => 'fos_rest.serializer',
            'session.storage' => 'session.storage.native',
            'swiftmailer.mailer' => 'swiftmailer.mailer.default',
            'swiftmailer.mailer.transport.mautic.transport.amazon' => 'mautic.transport.amazon',
            'swiftmailer.mailer.transport.mautic.transport.mandrill' => 'mautic.transport.mandrill',
            'swiftmailer.mailer.transport.mautic.transport.postmark' => 'mautic.transport.postmark',
            'swiftmailer.mailer.transport.mautic.transport.sendgrid' => 'mautic.transport.sendgrid',
            'swiftmailer.transport' => 'swiftmailer.mailer.default.transport',
            'translator' => 'translator.default',
        );
    }
    public function compile()
    {
        throw new LogicException('You cannot compile a dumped frozen container.');
    }
    protected function getAnnotationReaderService()
    {
        return $this->services['annotation_reader'] = new \Doctrine\Common\Annotations\FileCacheReader(new \Doctrine\Common\Annotations\AnnotationReader(), (__DIR__.'/annotations'), false);
    }
    protected function getBazinga_Oauth_Controller_LoginService()
    {
        return $this->services['bazinga.oauth.controller.login'] = new \Bazinga\OAuthServerBundle\Controller\LoginController($this->get('templating'), $this->get('security.context'), $this->get('bazinga.oauth.provider.token_provider'));
    }
    protected function getBazinga_Oauth_Controller_ServerService()
    {
        return $this->services['bazinga.oauth.controller.server'] = new \Bazinga\OAuthServerBundle\Controller\ServerController($this->get('router'), $this->get('templating'), $this->get('bazinga.oauth.server_service'), $this->get('bazinga.oauth.provider.token_provider'));
    }
    protected function getBazinga_Oauth_EventListener_ExceptionService()
    {
        return $this->services['bazinga.oauth.event_listener.exception'] = new \Bazinga\OAuthServerBundle\EventListener\OAuthExceptionListener();
    }
    protected function getBazinga_Oauth_EventListener_RequestService()
    {
        return $this->services['bazinga.oauth.event_listener.request'] = new \Mautic\ApiBundle\EventListener\OAuth1\OAuthRequestListener();
    }
    protected function getBazinga_Oauth_ServerServiceService()
    {
        $this->services['bazinga.oauth.server_service'] = $instance = new \Bazinga\OAuthServerBundle\Service\OAuthServerService(new \Bazinga\OAuthServerBundle\Doctrine\Provider\ConsumerProvider($this->get('bazinga.oauth.entity_manager'), 'Mautic\\ApiBundle\\Entity\\oAuth1\\Consumer'), $this->get('bazinga.oauth.provider.token_provider'), $this->get('mautic.api.oauth1.nonce_provider'), $this->get('logger'));
        $instance->addSignatureService($this->get('bazinga.oauth.signature.plaintext'));
        $instance->addSignatureService($this->get('bazinga.oauth.signature.hmac_sha1'));
        return $instance;
    }
    protected function getBazinga_Oauth_Signature_HmacSha1Service()
    {
        return $this->services['bazinga.oauth.signature.hmac_sha1'] = new \Bazinga\OAuthServerBundle\Service\Signature\OAuthHmacSha1Signature();
    }
    protected function getBazinga_Oauth_Signature_PlaintextService()
    {
        return $this->services['bazinga.oauth.signature.plaintext'] = new \Bazinga\OAuthServerBundle\Service\Signature\OAuthPlainTextSignature();
    }
    protected function getCacheClearerService()
    {
        return $this->services['cache_clearer'] = new \Symfony\Component\HttpKernel\CacheClearer\ChainCacheClearer(array());
    }
    protected function getCacheWarmerService()
    {
        return $this->services['cache_warmer'] = new \Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerAggregate(array(0 => new \Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplatePathsCacheWarmer(new \Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinder($this->get('kernel'), $this->get('templating.filename_parser'), ($this->targetDirs[2].'/Resources')), $this->get('templating.locator')), 1 => new \Symfony\Bundle\FrameworkBundle\CacheWarmer\RouterCacheWarmer($this->get('router')), 2 => new \Symfony\Bridge\Doctrine\CacheWarmer\ProxyCacheWarmer($this->get('doctrine'))));
    }
    protected function getDebug_EmergencyLoggerListenerService()
    {
        return $this->services['debug.emergency_logger_listener'] = new \Symfony\Component\HttpKernel\EventListener\ErrorsLoggerListener('emergency', $this->get('monolog.logger.emergency', ContainerInterface::NULL_ON_INVALID_REFERENCE));
    }
    protected function getDoctrineService()
    {
        return $this->services['doctrine'] = new \Doctrine\Bundle\DoctrineBundle\Registry($this, array('default' => 'doctrine.dbal.default_connection'), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
    }
    protected function getDoctrine_Dbal_ConnectionFactoryService()
    {
        return $this->services['doctrine.dbal.connection_factory'] = new \Doctrine\Bundle\DoctrineBundle\ConnectionFactory(array('array' => array('class' => 'Mautic\\CoreBundle\\Doctrine\\Type\\ArrayType', 'commented' => true), 'datetime' => array('class' => 'Mautic\\CoreBundle\\Doctrine\\Type\\UTCDateTimeType', 'commented' => true)));
    }
    protected function getDoctrine_Dbal_DefaultConnectionService()
    {
        $a = new \Symfony\Bridge\Doctrine\ContainerAwareEventManager($this);
        $a->addEventSubscriber($this->get('mautic.tblprefix_subscriber'));
        $a->addEventSubscriber($this->get('mautic.lead.doctrine.subscriber'));
        return $this->services['doctrine.dbal.default_connection'] = $this->get('doctrine.dbal.connection_factory')->createConnection(array('driver' => 'pdo_mysql', 'host' => 'localhost', 'port' => '3306', 'dbname' => 'autoMarket', 'user' => 'root', 'password' => 'labots.co', 'charset' => 'UTF8', 'driverOptions' => array()), new \Doctrine\DBAL\Configuration(), $a, array());
    }
    protected function getDoctrine_Orm_DefaultEntityListenerResolverService()
    {
        return $this->services['doctrine.orm.default_entity_listener_resolver'] = new \Doctrine\ORM\Mapping\DefaultEntityListenerResolver();
    }
    protected function getDoctrine_Orm_DefaultEntityManagerService()
    {
        $a = new \Doctrine\ORM\Mapping\Driver\StaticPHPDriver(array(0 => ($this->targetDirs[2].'/bundles/FormBundle/Entity'), 1 => ($this->targetDirs[2].'/bundles/CategoryBundle/Entity'), 2 => ($this->targetDirs[2].'/bundles/LeadBundle/Entity'), 3 => ($this->targetDirs[2].'/bundles/ReportBundle/Entity'), 4 => ($this->targetDirs[2].'/bundles/PageBundle/Entity'), 5 => ($this->targetDirs[2].'/bundles/CampaignBundle/Entity'), 6 => ($this->targetDirs[2].'/bundles/PointBundle/Entity'), 7 => ($this->targetDirs[2].'/bundles/ApiBundle/Entity'), 8 => ($this->targetDirs[2].'/bundles/UserBundle/Entity'), 9 => ($this->targetDirs[2].'/bundles/EmailBundle/Entity'), 10 => ($this->targetDirs[2].'/bundles/PluginBundle/Entity'), 11 => ($this->targetDirs[2].'/bundles/WebhookBundle/Entity'), 12 => ($this->targetDirs[2].'/bundles/CoreBundle/Entity'), 13 => ($this->targetDirs[2].'/bundles/AssetBundle/Entity')));
        $b = new \Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver(array(($this->targetDirs[3].'/vendor/friendsofsymfony/oauth-server-bundle/FOS/OAuthServerBundle/Resources/config/doctrine') => 'FOS\\OAuthServerBundle\\Entity'));
        $b->setGlobalBasename('mapping');
        $c = new \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain();
        $c->addDriver($a, 'Mautic\\FormBundle\\Entity');
        $c->addDriver($a, 'Mautic\\CategoryBundle\\Entity');
        $c->addDriver($a, 'Mautic\\LeadBundle\\Entity');
        $c->addDriver($a, 'Mautic\\ReportBundle\\Entity');
        $c->addDriver($a, 'Mautic\\PageBundle\\Entity');
        $c->addDriver($a, 'Mautic\\CampaignBundle\\Entity');
        $c->addDriver($a, 'Mautic\\PointBundle\\Entity');
        $c->addDriver($a, 'Mautic\\ApiBundle\\Entity');
        $c->addDriver($a, 'Mautic\\UserBundle\\Entity');
        $c->addDriver($a, 'Mautic\\EmailBundle\\Entity');
        $c->addDriver($a, 'Mautic\\PluginBundle\\Entity');
        $c->addDriver($a, 'Mautic\\WebhookBundle\\Entity');
        $c->addDriver($a, 'Mautic\\CoreBundle\\Entity');
        $c->addDriver($a, 'Mautic\\AssetBundle\\Entity');
        $c->addDriver($b, 'FOS\\OAuthServerBundle\\Entity');
        $c->addDriver(new \Doctrine\ORM\Mapping\Driver\XmlDriver(new \Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator(array(($this->targetDirs[3].'/vendor/willdurand/oauth-server-bundle/Resources/config/model') => 'Bazinga\\OAuthServerBundle\\Model'), '.orm.xml')), 'Bazinga\\OAuthServerBundle\\Model');
        $d = new \Doctrine\ORM\Configuration();
        $d->setEntityNamespaces(array('MauticFormBundle' => 'Mautic\\FormBundle\\Entity', 'MauticCategoryBundle' => 'Mautic\\CategoryBundle\\Entity', 'MauticLeadBundle' => 'Mautic\\LeadBundle\\Entity', 'MauticReportBundle' => 'Mautic\\ReportBundle\\Entity', 'MauticPageBundle' => 'Mautic\\PageBundle\\Entity', 'MauticCampaignBundle' => 'Mautic\\CampaignBundle\\Entity', 'MauticPointBundle' => 'Mautic\\PointBundle\\Entity', 'MauticApiBundle' => 'Mautic\\ApiBundle\\Entity', 'MauticUserBundle' => 'Mautic\\UserBundle\\Entity', 'MauticEmailBundle' => 'Mautic\\EmailBundle\\Entity', 'MauticPluginBundle' => 'Mautic\\PluginBundle\\Entity', 'MauticWebhookBundle' => 'Mautic\\WebhookBundle\\Entity', 'MauticCoreBundle' => 'Mautic\\CoreBundle\\Entity', 'MauticAssetBundle' => 'Mautic\\AssetBundle\\Entity', 'FOSOAuthServerBundle' => 'FOS\\OAuthServerBundle\\Entity'));
        $d->setMetadataCacheImpl($this->get('doctrine_cache.providers.doctrine.orm.default_metadata_cache'));
        $d->setQueryCacheImpl($this->get('doctrine_cache.providers.doctrine.orm.default_query_cache'));
        $d->setResultCacheImpl($this->get('doctrine_cache.providers.doctrine.orm.default_result_cache'));
        $d->setMetadataDriverImpl($c);
        $d->setProxyDir((__DIR__.'/doctrine/orm/Proxies'));
        $d->setProxyNamespace('Proxies');
        $d->setAutoGenerateProxyClasses(false);
        $d->setClassMetadataFactoryName('Doctrine\\ORM\\Mapping\\ClassMetadataFactory');
        $d->setDefaultRepositoryClassName('Doctrine\\ORM\\EntityRepository');
        $d->setNamingStrategy(new \Doctrine\ORM\Mapping\DefaultNamingStrategy());
        $d->setEntityListenerResolver($this->get('doctrine.orm.default_entity_listener_resolver'));
        $this->services['doctrine.orm.default_entity_manager'] = $instance = \Doctrine\ORM\EntityManager::create($this->get('doctrine.dbal.default_connection'), $d);
        $this->get('doctrine.orm.default_manager_configurator')->configure($instance);
        return $instance;
    }
    protected function getDoctrine_Orm_DefaultManagerConfiguratorService()
    {
        return $this->services['doctrine.orm.default_manager_configurator'] = new \Doctrine\Bundle\DoctrineBundle\ManagerConfigurator(array(), array());
    }
    protected function getDoctrine_Orm_Validator_UniqueService()
    {
        return $this->services['doctrine.orm.validator.unique'] = new \Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator($this->get('doctrine'));
    }
    protected function getDoctrine_Orm_ValidatorInitializerService()
    {
        return $this->services['doctrine.orm.validator_initializer'] = new \Symfony\Bridge\Doctrine\Validator\DoctrineInitializer($this->get('doctrine'));
    }
    protected function getDoctrineCache_Providers_Doctrine_Orm_DefaultMetadataCacheService()
    {
        $this->services['doctrine_cache.providers.doctrine.orm.default_metadata_cache'] = $instance = new \Doctrine\Common\Cache\ArrayCache();
        $instance->setNamespace('sf2orm_default_53dc49ffbf61355e45b4a33b1546cd42992f55fbf051caa99a17c560aada8704');
        return $instance;
    }
    protected function getDoctrineCache_Providers_Doctrine_Orm_DefaultQueryCacheService()
    {
        $this->services['doctrine_cache.providers.doctrine.orm.default_query_cache'] = $instance = new \Doctrine\Common\Cache\ArrayCache();
        $instance->setNamespace('sf2orm_default_53dc49ffbf61355e45b4a33b1546cd42992f55fbf051caa99a17c560aada8704');
        return $instance;
    }
    protected function getDoctrineCache_Providers_Doctrine_Orm_DefaultResultCacheService()
    {
        $this->services['doctrine_cache.providers.doctrine.orm.default_result_cache'] = $instance = new \Doctrine\Common\Cache\ArrayCache();
        $instance->setNamespace('sf2orm_default_53dc49ffbf61355e45b4a33b1546cd42992f55fbf051caa99a17c560aada8704');
        return $instance;
    }
    protected function getEventDispatcherService()
    {
        $this->services['event_dispatcher'] = $instance = new \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher($this);
        $instance->addListenerService('kernel.request', array(0 => 'knp_menu.listener.voters', 1 => 'onKernelRequest'), 0);
        $instance->addListenerService('kernel.request', array(0 => 'bazinga.oauth.event_listener.request', 1 => 'onEarlyKernelRequest'), 255);
        $instance->addListenerService('kernel.exception', array(0 => 'bazinga.oauth.event_listener.exception', 1 => 'onKernelException'), 0);
        $instance->addListenerService('kernel.request', array(0 => 'fos_rest.body_listener', 1 => 'onKernelRequest'), 10);
        $instance->addListenerService('oneup_uploader.validation', array(0 => 'oneup_uploader.validation_listener.max_size', 1 => 'onValidate'), 0);
        $instance->addListenerService('oneup_uploader.validation', array(0 => 'oneup_uploader.validation_listener.allowed_mimetype', 1 => 'onValidate'), 0);
        $instance->addListenerService('oneup_uploader.validation', array(0 => 'oneup_uploader.validation_listener.disallowed_mimetype', 1 => 'onValidate'), 0);
        $instance->addListenerService('kernel.exception', array(0 => 'mautic.exception.listener', 1 => 'onKernelException'), 255);
        $instance->addListenerService('fos_oauth_server.pre_authorization_process', array(0 => 'mautic.api.oauth.event_listener', 1 => 'onPreAuthorizationProcess'), 0);
        $instance->addListenerService('fos_oauth_server.post_authorization_process', array(0 => 'mautic.api.oauth.event_listener', 1 => 'onPostAuthorizationProcess'), 0);
        $instance->addSubscriberService('response_listener', 'Symfony\\Component\\HttpKernel\\EventListener\\ResponseListener');
        $instance->addSubscriberService('streamed_response_listener', 'Symfony\\Component\\HttpKernel\\EventListener\\StreamedResponseListener');
        $instance->addSubscriberService('locale_listener', 'Symfony\\Component\\HttpKernel\\EventListener\\LocaleListener');
        $instance->addSubscriberService('debug.emergency_logger_listener', 'Symfony\\Component\\HttpKernel\\EventListener\\ErrorsLoggerListener');
        $instance->addSubscriberService('session_listener', 'Symfony\\Bundle\\FrameworkBundle\\EventListener\\SessionListener');
        $instance->addSubscriberService('session.save_listener', 'Symfony\\Component\\HttpKernel\\EventListener\\SaveSessionListener');
        $instance->addSubscriberService('fragment.listener', 'Symfony\\Component\\HttpKernel\\EventListener\\FragmentListener');
        $instance->addSubscriberService('router_listener', 'Symfony\\Component\\HttpKernel\\EventListener\\RouterListener');
        $instance->addSubscriberService('security.firewall', 'Symfony\\Component\\Security\\Http\\Firewall');
        $instance->addSubscriberService('security.rememberme.response_listener', 'Symfony\\Component\\Security\\Http\\RememberMe\\ResponseListener');
        $instance->addSubscriberService('swiftmailer.email_sender.listener', 'Symfony\\Bundle\\SwiftmailerBundle\\EventListener\\EmailSenderListener');
        $instance->addSubscriberService('mautic.core.subscriber', 'Mautic\\CoreBundle\\EventListener\\CoreSubscriber');
        $instance->addSubscriberService('mautic.core.auditlog.subscriber', 'Mautic\\CoreBundle\\EventListener\\AuditLogSubscriber');
        $instance->addSubscriberService('mautic.core.configbundle.subscriber', 'Mautic\\CoreBundle\\EventListener\\ConfigSubscriber');
        $instance->addSubscriberService('mautic.core.errorhandler.subscriber', 'Mautic\\CoreBundle\\EventListener\\ErrorHandlingListener');
        $instance->addSubscriberService('mautic.form.subscriber', 'Mautic\\FormBundle\\EventListener\\FormSubscriber');
        $instance->addSubscriberService('mautic.form.pagebundle.subscriber', 'Mautic\\FormBundle\\EventListener\\PageSubscriber');
        $instance->addSubscriberService('mautic.form.pointbundle.subscriber', 'Mautic\\FormBundle\\EventListener\\PointSubscriber');
        $instance->addSubscriberService('mautic.form.reportbundle.subscriber', 'Mautic\\FormBundle\\EventListener\\ReportSubscriber');
        $instance->addSubscriberService('mautic.form.campaignbundle.subscriber', 'Mautic\\FormBundle\\EventListener\\CampaignSubscriber');
        $instance->addSubscriberService('mautic.form.calendarbundle.subscriber', 'Mautic\\FormBundle\\EventListener\\CalendarSubscriber');
        $instance->addSubscriberService('mautic.form.leadbundle.subscriber', 'Mautic\\FormBundle\\EventListener\\LeadSubscriber');
        $instance->addSubscriberService('mautic.form.emailbundle.subscriber', 'Mautic\\FormBundle\\EventListener\\EmailSubscriber');
        $instance->addSubscriberService('mautic.form.search.subscriber', 'Mautic\\FormBundle\\EventListener\\SearchSubscriber');
        $instance->addSubscriberService('mautic.form.webhook.subscriber', 'Mautic\\FormBundle\\EventListener\\WebhookSubscriber');
        $instance->addSubscriberService('mautic.category.subscriber', 'Mautic\\CategoryBundle\\EventListener\\CategorySubscriber');
        $instance->addSubscriberService('mautic.lead.subscriber', 'Mautic\\LeadBundle\\EventListener\\LeadSubscriber');
        $instance->addSubscriberService('mautic.lead.emailbundle.subscriber', 'Mautic\\LeadBundle\\EventListener\\EmailSubscriber');
        $instance->addSubscriberService('mautic.lead.formbundle.subscriber', 'Mautic\\LeadBundle\\EventListener\\FormSubscriber');
        $instance->addSubscriberService('mautic.lead.campaignbundle.subscriber', 'Mautic\\LeadBundle\\EventListener\\CampaignSubscriber');
        $instance->addSubscriberService('mautic.lead.reportbundle.subscriber', 'Mautic\\LeadBundle\\EventListener\\ReportSubscriber');
        $instance->addSubscriberService('mautic.lead.calendarbundle.subscriber', 'Mautic\\LeadBundle\\EventListener\\CalendarSubscriber');
        $instance->addSubscriberService('mautic.lead.pointbundle.subscriber', 'Mautic\\LeadBundle\\EventListener\\PointSubscriber');
        $instance->addSubscriberService('mautic.lead.search.subscriber', 'Mautic\\LeadBundle\\EventListener\\SearchSubscriber');
        $instance->addSubscriberService('mautic.webhook.subscriber', 'Mautic\\LeadBundle\\EventListener\\WebhookSubscriber');
        $instance->addSubscriberService('mautic.report.search.subscriber', 'Mautic\\ReportBundle\\EventListener\\SearchSubscriber');
        $instance->addSubscriberService('mautic.report.report.subscriber', 'Mautic\\ReportBundle\\EventListener\\ReportSubscriber');
        $instance->addSubscriberService('mautic.page.subscriber', 'Mautic\\PageBundle\\EventListener\\PageSubscriber');
        $instance->addSubscriberService('mautic.pagebuilder.subscriber', 'Mautic\\PageBundle\\EventListener\\BuilderSubscriber');
        $instance->addSubscriberService('mautic.page.pointbundle.subscriber', 'Mautic\\PageBundle\\EventListener\\PointSubscriber');
        $instance->addSubscriberService('mautic.page.reportbundle.subscriber', 'Mautic\\PageBundle\\EventListener\\ReportSubscriber');
        $instance->addSubscriberService('mautic.page.campaignbundle.subscriber', 'Mautic\\PageBundle\\EventListener\\CampaignSubscriber');
        $instance->addSubscriberService('mautic.page.leadbundle.subscriber', 'Mautic\\PageBundle\\EventListener\\LeadSubscriber');
        $instance->addSubscriberService('mautic.page.calendarbundle.subscriber', 'Mautic\\PageBundle\\EventListener\\CalendarSubscriber');
        $instance->addSubscriberService('mautic.page.configbundle.subscriber', 'Mautic\\PageBundle\\EventListener\\ConfigSubscriber');
        $instance->addSubscriberService('mautic.page.search.subscriber', 'Mautic\\PageBundle\\EventListener\\SearchSubscriber');
        $instance->addSubscriberService('mautic.page.webhook.subscriber', 'Mautic\\PageBundle\\EventListener\\WebhookSubscriber');
        $instance->addSubscriberService('mautic.campaign.subscriber', 'Mautic\\CampaignBundle\\EventListener\\CampaignSubscriber');
        $instance->addSubscriberService('mautic.campaign.leadbundle.subscriber', 'Mautic\\CampaignBundle\\EventListener\\LeadSubscriber');
        $instance->addSubscriberService('mautic.campaign.calendarbundle.subscriber', 'Mautic\\CampaignBundle\\EventListener\\CalendarSubscriber');
        $instance->addSubscriberService('mautic.campaign.pointbundle.subscriber', 'Mautic\\CampaignBundle\\EventListener\\PointSubscriber');
        $instance->addSubscriberService('mautic.campaign.search.subscriber', 'Mautic\\CampaignBundle\\EventListener\\SearchSubscriber');
        $instance->addSubscriberService('mautic.point.subscriber', 'Mautic\\PointBundle\\EventListener\\PointSubscriber');
        $instance->addSubscriberService('mautic.point.leadbundle.subscriber', 'Mautic\\PointBundle\\EventListener\\LeadSubscriber');
        $instance->addSubscriberService('mautic.point.search.subscriber', 'Mautic\\PointBundle\\EventListener\\SearchSubscriber');
        $instance->addSubscriberService('mautic.api.subscriber', 'Mautic\\ApiBundle\\EventListener\\ApiSubscriber');
        $instance->addSubscriberService('mautic.api.configbundle.subscriber', 'Mautic\\ApiBundle\\EventListener\\ConfigSubscriber');
        $instance->addSubscriberService('mautic.api.search.subscriber', 'Mautic\\ApiBundle\\EventListener\\SearchSubscriber');
        $instance->addSubscriberService('mautic.user.subscriber', 'Mautic\\UserBundle\\EventListener\\UserSubscriber');
        $instance->addSubscriberService('mautic.user.search.subscriber', 'Mautic\\UserBundle\\EventListener\\SearchSubscriber');
        $instance->addSubscriberService('mautic.email.subscriber', 'Mautic\\EmailBundle\\EventListener\\EmailSubscriber');
        $instance->addSubscriberService('mautic.emailbuilder.subscriber', 'Mautic\\EmailBundle\\EventListener\\BuilderSubscriber');
        $instance->addSubscriberService('mautic.email.campaignbundle.subscriber', 'Mautic\\EmailBundle\\EventListener\\CampaignSubscriber');
        $instance->addSubscriberService('mautic.email.formbundle.subscriber', 'Mautic\\EmailBundle\\EventListener\\FormSubscriber');
        $instance->addSubscriberService('mautic.email.reportbundle.subscriber', 'Mautic\\EmailBundle\\EventListener\\ReportSubscriber');
        $instance->addSubscriberService('mautic.email.leadbundle.subscriber', 'Mautic\\EmailBundle\\EventListener\\LeadSubscriber');
        $instance->addSubscriberService('mautic.email.pointbundle.subscriber', 'Mautic\\EmailBundle\\EventListener\\PointSubscriber');
        $instance->addSubscriberService('mautic.email.calendarbundle.subscriber', 'Mautic\\EmailBundle\\EventListener\\CalendarSubscriber');
        $instance->addSubscriberService('mautic.email.search.subscriber', 'Mautic\\EmailBundle\\EventListener\\SearchSubscriber');
        $instance->addSubscriberService('mautic.email.webhook.subscriber', 'Mautic\\EmailBundle\\EventListener\\WebhookSubscriber');
        $instance->addSubscriberService('mautic.email.configbundle.subscriber', 'Mautic\\EmailBundle\\EventListener\\ConfigSubscriber');
        $instance->addSubscriberService('mautic.email.pagebundle.subscriber', 'Mautic\\EmailBundle\\EventListener\\PageSubscriber');
        $instance->addSubscriberService('mautic.plugin.pointbundle.subscriber', 'Mautic\\PluginBundle\\EventListener\\PointSubscriber');
        $instance->addSubscriberService('mautic.plugin.formbundle.subscriber', 'Mautic\\PluginBundle\\EventListener\\FormSubscriber');
        $instance->addSubscriberService('mautic.plugin.campaignbundle.subscriber', 'Mautic\\PluginBundle\\EventListener\\CampaignSubscriber');
        $instance->addSubscriberService('mautic.config.subscriber', 'Mautic\\ConfigBundle\\EventListener\\ConfigSubscriber');
        $instance->addSubscriberService('mautic.webhook.lead.subscriber', 'Mautic\\WebhookBundle\\EventListener\\LeadSubscriber');
        $instance->addSubscriberService('mautic.webhook.form.subscriber', 'Mautic\\WebhookBundle\\EventListener\\FormSubscriber');
        $instance->addSubscriberService('mautic.webhook.email.subscriber', 'Mautic\\WebhookBundle\\EventListener\\EmailSubscriber');
        $instance->addSubscriberService('mautic.webhook.page.hit.subscriber', 'Mautic\\WebhookBundle\\EventListener\\PageSubscriber');
        $instance->addSubscriberService('mautic.webhook.config.subscriber', 'Mautic\\WebhookBundle\\EventListener\\ConfigSubscriber');
        $instance->addSubscriberService('mautic.webhook.audit.subscriber', 'Mautic\\WebhookBundle\\EventListener\\WebhookSubscriber');
        $instance->addSubscriberService('mautic.asset.subscriber', 'Mautic\\AssetBundle\\EventListener\\AssetSubscriber');
        $instance->addSubscriberService('mautic.asset.pointbundle.subscriber', 'Mautic\\AssetBundle\\EventListener\\PointSubscriber');
        $instance->addSubscriberService('mautic.asset.formbundle.subscriber', 'Mautic\\AssetBundle\\EventListener\\FormSubscriber');
        $instance->addSubscriberService('mautic.asset.campaignbundle.subscriber', 'Mautic\\AssetBundle\\EventListener\\CampaignSubscriber');
        $instance->addSubscriberService('mautic.asset.reportbundle.subscriber', 'Mautic\\AssetBundle\\EventListener\\ReportSubscriber');
        $instance->addSubscriberService('mautic.asset.builder.subscriber', 'Mautic\\AssetBundle\\EventListener\\BuilderSubscriber');
        $instance->addSubscriberService('mautic.asset.leadbundle.subscriber', 'Mautic\\AssetBundle\\EventListener\\LeadSubscriber');
        $instance->addSubscriberService('mautic.asset.pagebundle.subscriber', 'Mautic\\AssetBundle\\EventListener\\PageSubscriber');
        $instance->addSubscriberService('mautic.asset.emailbundle.subscriber', 'Mautic\\AssetBundle\\EventListener\\EmailSubscriber');
        $instance->addSubscriberService('mautic.asset.configbundle.subscriber', 'Mautic\\AssetBundle\\EventListener\\ConfigSubscriber');
        $instance->addSubscriberService('mautic.asset.search.subscriber', 'Mautic\\AssetBundle\\EventListener\\SearchSubscriber');
        $instance->addSubscriberService('oneup_uploader.pre_upload', 'Mautic\\AssetBundle\\EventListener\\UploadSubscriber');
        $instance->addSubscriberService('mautic.cloudstorage.remoteassetbrowse.subscriber', 'MauticPlugin\\MauticCloudStorageBundle\\EventListener\\RemoteAssetBrowseSubscriber');
        return $instance;
    }
    protected function getFileLocatorService()
    {
        return $this->services['file_locator'] = new \Symfony\Component\HttpKernel\Config\FileLocator($this->get('kernel'), ($this->targetDirs[2].'/Resources'));
    }
    protected function getFilesystemService()
    {
        return $this->services['filesystem'] = new \Symfony\Component\Filesystem\Filesystem();
    }
    protected function getForm_CsrfProviderService()
    {
        return $this->services['form.csrf_provider'] = new \Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfTokenManagerAdapter($this->get('security.csrf.token_manager'));
    }
    protected function getForm_FactoryService()
    {
        return $this->services['form.factory'] = new \Symfony\Component\Form\FormFactory($this->get('form.registry'), $this->get('form.resolved_type_factory'));
    }
    protected function getForm_RegistryService()
    {
        return $this->services['form.registry'] = new \Symfony\Component\Form\FormRegistry(array(0 => new \Symfony\Component\Form\Extension\DependencyInjection\DependencyInjectionExtension($this, array('form' => 'form.type.form', 'birthday' => 'form.type.birthday', 'checkbox' => 'form.type.checkbox', 'choice' => 'form.type.choice', 'collection' => 'form.type.collection', 'country' => 'form.type.country', 'date' => 'form.type.date', 'datetime' => 'form.type.datetime', 'email' => 'form.type.email', 'file' => 'form.type.file', 'hidden' => 'form.type.hidden', 'integer' => 'form.type.integer', 'language' => 'form.type.language', 'locale' => 'form.type.locale', 'money' => 'form.type.money', 'number' => 'form.type.number', 'password' => 'form.type.password', 'percent' => 'form.type.percent', 'radio' => 'form.type.radio', 'repeated' => 'form.type.repeated', 'search' => 'form.type.search', 'textarea' => 'form.type.textarea', 'text' => 'form.type.text', 'time' => 'form.type.time', 'timezone' => 'form.type.timezone', 'url' => 'form.type.url', 'button' => 'form.type.button', 'submit' => 'form.type.submit', 'reset' => 'form.type.reset', 'currency' => 'form.type.currency', 'entity' => 'form.type.entity', 'fos_oauth_server_authorize' => 'fos_oauth_server.authorize.form.type', 'spacer' => 'mautic.form.type.spacer', 'tel' => 'mautic.form.type.tel', 'button_group' => 'mautic.form.type.button_group', 'yesno_button_group' => 'mautic.form.type.yesno_button_group', 'standalone_button' => 'mautic.form.type.standalone_button', 'form_buttons' => 'mautic.form.type.form_buttons', 'hidden_entity' => 'mautic.form.type.hidden_entity', 'sortablelist' => 'mautic.form.type.sortablelist', 'dynamiclist' => 'mautic.form.type.dynamiclist', 'coreconfig' => 'mautic.form.type.coreconfig', 'theme_list' => 'mautic.form.type.theme_list', 'mauticform' => 'mautic.form.type.form', 'formfield' => 'mautic.form.type.field', 'formaction' => 'mautic.form.type.action', 'formfield_text' => 'mautic.form.type.field_propertytext', 'formfield_placeholder' => 'mautic.form.type.field_propertyplaceholder', 'formfield_select' => 'mautic.form.type.field_propertyselect', 'formfield_captcha' => 'mautic.form.type.field_propertycaptcha', 'formfield_group' => 'mautic.form.type.field_propertygroup', 'pointaction_formsubmit' => 'mautic.form.type.pointaction_formsubmit', 'form_list' => 'mautic.form.type.form_list', 'campaignevent_formsubmit' => 'mautic.form.type.campaignevent_formsubmit', 'campaignevent_form_field_value' => 'mautic.form.type.campaignevent_form_field_value', 'form_submitaction_sendemail' => 'mautic.form.type.form_submitaction_sendemail', 'category' => 'mautic.form.type.category', 'category_form' => 'mautic.form.type.category_form', 'category_bundles_form' => 'mautic.form.type.category_bundles_form', 'lead' => 'mautic.form.type.lead', 'leadlist' => 'mautic.form.type.leadlist', 'leadlist_choices' => 'mautic.form.type.leadlist_choices', 'leadlist_filter' => 'mautic.form.type.leadlist_filter', 'leadfield' => 'mautic.form.type.leadfield', 'lead_submitaction_pointschange' => 'mautic.form.type.lead.submitaction.pointschange', 'leadlist_action_type' => 'mautic.form.type.lead.submitaction.changelist', 'leadpoints_trigger' => 'mautic.form.type.leadpoints_trigger', 'leadpoints_action' => 'mautic.form.type.leadpoints_action', 'leadlist_trigger' => 'mautic.form.type.leadlist_trigger', 'leadlist_action' => 'mautic.form.type.leadlist_action', 'updatelead_action' => 'mautic.form.type.updatelead_action', 'leadnote' => 'mautic.form.type.leadnote', 'lead_import' => 'mautic.form.type.lead_import', 'lead_field_import' => 'mautic.form.type.lead_field_import', 'lead_quickemail' => 'mautic.form.type.lead_quickemail', 'lead_tags' => 'mautic.form.type.lead_tags', 'lead_tag' => 'mautic.form.type.lead_tag', 'modify_lead_tags' => 'mautic.form.type.modify_lead_tags', 'lead_batch' => 'mautic.form.type.lead_batch', 'lead_batch_dnc' => 'mautic.form.type.lead_batch_dnc', 'lead_merge' => 'mautic.form.type.lead_merge', 'campaignevent_lead_field_value' => 'mautic.form.type.campaignevent_lead_field_value', 'leadfields_choices' => 'mautic.form.type.lead_fields', 'report' => 'mautic.form.type.report', 'filter_selector' => 'mautic.form.type.filter_selector', 'table_order' => 'mautic.form.type.table_order', 'report_filters' => 'mautic.form.type.report_filters', 'page' => 'mautic.form.type.page', 'pagevariant' => 'mautic.form.type.pagevariant', 'pointaction_pagehit' => 'mautic.form.type.pointaction_pointhit', 'pointaction_urlhit' => 'mautic.form.type.pointaction_urlhit', 'campaignevent_pagehit' => 'mautic.form.type.pagehit.campaign_trigger', 'page_list' => 'mautic.form.type.pagelist', 'page_abtest_settings' => 'mautic.form.type.page_abtest_settings', 'page_publish_dates' => 'mautic.form.type.page_publish_dates', 'pageconfig' => 'mautic.form.type.pageconfig', 'slideshow_config' => 'mautic.form.type.slideshow_config', 'slideshow_slide_config' => 'mautic.form.type.slideshow_slide_config', 'redirect_list' => 'mautic.form.type.redirect_list', 'campaign' => 'mautic.campaign.type.form', 'campaignevent' => 'mautic.campaignrange.type.action', 'campaign_list' => 'mautic.campaign.type.campaignlist', 'campaignevent_leadchange' => 'mautic.campaign.type.trigger.leadchange', 'campaignevent_addremovelead' => 'mautic.campaign.type.action.addremovelead', 'campaignevent_canvassettings' => 'mautic.campaign.type.canvassettings', 'campaign_leadsource' => 'mautic.campaign.type.leadsource', 'point' => 'mautic.point.type.form', 'pointaction' => 'mautic.point.type.action', 'pointtrigger' => 'mautic.pointtrigger.type.form', 'pointtriggerevent' => 'mautic.pointtrigger.type.action', 'genericpoint_settings' => 'mautic.point.type.genericpoint_settings', 'client' => 'mautic.form.type.apiclients', 'apiconfig' => 'mautic.form.type.apiconfig', 'user' => 'mautic.form.type.user', 'role' => 'mautic.form.type.role', 'permissions' => 'mautic.form.type.permissions', 'permissionlist' => 'mautic.form.type.permissionlist', 'passwordreset' => 'mautic.form.type.passwordreset', 'user_list' => 'mautic.form.type.user_list', 'emailform' => 'mautic.form.type.email', 'emailvariant' => 'mautic.form.type.emailvariant', 'email_list' => 'mautic.form.type.email_list', 'emailopen_list' => 'mautic.form.type.emailopen_list', 'emailsend_list' => 'mautic.form.type.emailsend_list', 'email_submitaction_useremail' => 'mautic.form.type.formsubmit_sendemail_admin', 'email_abtest_settings' => 'mautic.email.type.email_abtest_settings', 'batch_send' => 'mautic.email.type.batch_send', 'emailconfig' => 'mautic.form.type.emailconfig', 'monitored_mailboxes' => 'mautic.form.type.coreconfig_monitored_mailboxes', 'monitored_email' => 'mautic.form.type.coreconfig_monitored_email', 'integration_details' => 'mautic.form.type.integration.details', 'integration_featuresettings' => 'mautic.form.type.integration.settings', 'integration_fields' => 'mautic.form.type.integration.fields', 'integration_keys' => 'mautic.form.type.integration.keys', 'integration_list' => 'mautic.form.type.integration.list', 'integration_config' => 'mautic.form.type.integration.config', 'config' => 'mautic.form.type.config', 'webhook' => 'mautic.form.type.webhook', 'webhookconfig' => 'mautic.form.type.webhookconfig', 'asset' => 'mautic.form.type.asset', 'pointaction_assetdownload' => 'mautic.form.type.pointaction_assetdownload', 'campaignevent_assetdownload' => 'mautic.form.type.campaignevent_assetdownload', 'asset_submitaction_downloadfile' => 'mautic.form.type.formsubmit_assetdownload', 'asset_list' => 'mautic.form.type.assetlist', 'assetconfig' => 'mautic.form.type.assetconfig', 'emailmarketing_mailchimp' => 'mautic.form.type.emailmarketing.mailchimp', 'emailmarketing_constantcontact' => 'mautic.form.type.emailmarketing.constantcontact', 'emailmarketing_icontact' => 'mautic.form.type.emailmarketing.icontact', 'cloudstorage_openstack' => 'mautic.form.type.cloudstorage.openstack', 'cloudstorage_rackspace' => 'mautic.form.type.cloudstorage.rackspace', 'socialmedia_facebook' => 'mautic.form.type.social.facebook', 'socialmedia_twitter' => 'mautic.form.type.social.twitter', 'socialmedia_googleplus' => 'mautic.form.type.social.googleplus', 'socialmedia_linkedin' => 'mautic.form.type.social.linkedin'), array('form' => array(0 => 'form.type_extension.form.http_foundation', 1 => 'form.type_extension.form.validator', 2 => 'form.type_extension.csrf', 3 => 'fos_rest.form.extension.csrf_disable'), 'repeated' => array(0 => 'form.type_extension.repeated.validator'), 'submit' => array(0 => 'form.type_extension.submit.validator')), array(0 => 'form.type_guesser.validator', 1 => 'form.type_guesser.doctrine'))), $this->get('form.resolved_type_factory'));
    }
    protected function getForm_ResolvedTypeFactoryService()
    {
        return $this->services['form.resolved_type_factory'] = new \Symfony\Component\Form\ResolvedFormTypeFactory();
    }
    protected function getForm_Type_BirthdayService()
    {
        return $this->services['form.type.birthday'] = new \Symfony\Component\Form\Extension\Core\Type\BirthdayType();
    }
    protected function getForm_Type_ButtonService()
    {
        return $this->services['form.type.button'] = new \Symfony\Component\Form\Extension\Core\Type\ButtonType();
    }
    protected function getForm_Type_CheckboxService()
    {
        return $this->services['form.type.checkbox'] = new \Symfony\Component\Form\Extension\Core\Type\CheckboxType();
    }
    protected function getForm_Type_ChoiceService()
    {
        return $this->services['form.type.choice'] = new \Symfony\Component\Form\Extension\Core\Type\ChoiceType();
    }
    protected function getForm_Type_CollectionService()
    {
        return $this->services['form.type.collection'] = new \Symfony\Component\Form\Extension\Core\Type\CollectionType();
    }
    protected function getForm_Type_CountryService()
    {
        return $this->services['form.type.country'] = new \Symfony\Component\Form\Extension\Core\Type\CountryType();
    }
    protected function getForm_Type_CurrencyService()
    {
        return $this->services['form.type.currency'] = new \Symfony\Component\Form\Extension\Core\Type\CurrencyType();
    }
    protected function getForm_Type_DateService()
    {
        return $this->services['form.type.date'] = new \Symfony\Component\Form\Extension\Core\Type\DateType();
    }
    protected function getForm_Type_DatetimeService()
    {
        return $this->services['form.type.datetime'] = new \Symfony\Component\Form\Extension\Core\Type\DateTimeType();
    }
    protected function getForm_Type_EmailService()
    {
        return $this->services['form.type.email'] = new \Symfony\Component\Form\Extension\Core\Type\EmailType();
    }
    protected function getForm_Type_EntityService()
    {
        return $this->services['form.type.entity'] = new \Symfony\Bridge\Doctrine\Form\Type\EntityType($this->get('doctrine'));
    }
    protected function getForm_Type_FileService()
    {
        return $this->services['form.type.file'] = new \Symfony\Component\Form\Extension\Core\Type\FileType();
    }
    protected function getForm_Type_FormService()
    {
        return $this->services['form.type.form'] = new \Symfony\Component\Form\Extension\Core\Type\FormType($this->get('property_accessor'));
    }
    protected function getForm_Type_HiddenService()
    {
        return $this->services['form.type.hidden'] = new \Symfony\Component\Form\Extension\Core\Type\HiddenType();
    }
    protected function getForm_Type_IntegerService()
    {
        return $this->services['form.type.integer'] = new \Symfony\Component\Form\Extension\Core\Type\IntegerType();
    }
    protected function getForm_Type_LanguageService()
    {
        return $this->services['form.type.language'] = new \Symfony\Component\Form\Extension\Core\Type\LanguageType();
    }
    protected function getForm_Type_LocaleService()
    {
        return $this->services['form.type.locale'] = new \Symfony\Component\Form\Extension\Core\Type\LocaleType();
    }
    protected function getForm_Type_MoneyService()
    {
        return $this->services['form.type.money'] = new \Symfony\Component\Form\Extension\Core\Type\MoneyType();
    }
    protected function getForm_Type_NumberService()
    {
        return $this->services['form.type.number'] = new \Symfony\Component\Form\Extension\Core\Type\NumberType();
    }
    protected function getForm_Type_PasswordService()
    {
        return $this->services['form.type.password'] = new \Symfony\Component\Form\Extension\Core\Type\PasswordType();
    }
    protected function getForm_Type_PercentService()
    {
        return $this->services['form.type.percent'] = new \Symfony\Component\Form\Extension\Core\Type\PercentType();
    }
    protected function getForm_Type_RadioService()
    {
        return $this->services['form.type.radio'] = new \Symfony\Component\Form\Extension\Core\Type\RadioType();
    }
    protected function getForm_Type_RepeatedService()
    {
        return $this->services['form.type.repeated'] = new \Symfony\Component\Form\Extension\Core\Type\RepeatedType();
    }
    protected function getForm_Type_ResetService()
    {
        return $this->services['form.type.reset'] = new \Symfony\Component\Form\Extension\Core\Type\ResetType();
    }
    protected function getForm_Type_SearchService()
    {
        return $this->services['form.type.search'] = new \Symfony\Component\Form\Extension\Core\Type\SearchType();
    }
    protected function getForm_Type_SubmitService()
    {
        return $this->services['form.type.submit'] = new \Symfony\Component\Form\Extension\Core\Type\SubmitType();
    }
    protected function getForm_Type_TextService()
    {
        return $this->services['form.type.text'] = new \Symfony\Component\Form\Extension\Core\Type\TextType();
    }
    protected function getForm_Type_TextareaService()
    {
        return $this->services['form.type.textarea'] = new \Symfony\Component\Form\Extension\Core\Type\TextareaType();
    }
    protected function getForm_Type_TimeService()
    {
        return $this->services['form.type.time'] = new \Symfony\Component\Form\Extension\Core\Type\TimeType();
    }
    protected function getForm_Type_TimezoneService()
    {
        return $this->services['form.type.timezone'] = new \Symfony\Component\Form\Extension\Core\Type\TimezoneType();
    }
    protected function getForm_Type_UrlService()
    {
        return $this->services['form.type.url'] = new \Symfony\Component\Form\Extension\Core\Type\UrlType();
    }
    protected function getForm_TypeExtension_CsrfService()
    {
        return $this->services['form.type_extension.csrf'] = new \Symfony\Component\Form\Extension\Csrf\Type\FormTypeCsrfExtension($this->get('form.csrf_provider'), true, '_token', $this->get('translator.default'), 'validators');
    }
    protected function getForm_TypeExtension_Form_HttpFoundationService()
    {
        return $this->services['form.type_extension.form.http_foundation'] = new \Symfony\Component\Form\Extension\HttpFoundation\Type\FormTypeHttpFoundationExtension(new \Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler());
    }
    protected function getForm_TypeExtension_Form_ValidatorService()
    {
        return $this->services['form.type_extension.form.validator'] = new \Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension($this->get('validator'));
    }
    protected function getForm_TypeExtension_Repeated_ValidatorService()
    {
        return $this->services['form.type_extension.repeated.validator'] = new \Symfony\Component\Form\Extension\Validator\Type\RepeatedTypeValidatorExtension();
    }
    protected function getForm_TypeExtension_Submit_ValidatorService()
    {
        return $this->services['form.type_extension.submit.validator'] = new \Symfony\Component\Form\Extension\Validator\Type\SubmitTypeValidatorExtension();
    }
    protected function getForm_TypeGuesser_DoctrineService()
    {
        return $this->services['form.type_guesser.doctrine'] = new \Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser($this->get('doctrine'));
    }
    protected function getForm_TypeGuesser_ValidatorService()
    {
        return $this->services['form.type_guesser.validator'] = new \Symfony\Component\Form\Extension\Validator\ValidatorTypeGuesser($this->get('validator'));
    }
    protected function getFosOauthServer_AccessTokenManager_DefaultService()
    {
        return $this->services['fos_oauth_server.access_token_manager.default'] = new \FOS\OAuthServerBundle\Entity\AccessTokenManager($this->get('fos_oauth_server.entity_manager'), 'Mautic\\ApiBundle\\Entity\\oAuth2\\AccessToken');
    }
    protected function getFosOauthServer_AuthCodeManager_DefaultService()
    {
        return $this->services['fos_oauth_server.auth_code_manager.default'] = new \FOS\OAuthServerBundle\Entity\AuthCodeManager($this->get('fos_oauth_server.entity_manager'), 'Mautic\\ApiBundle\\Entity\\oAuth2\\AuthCode');
    }
    protected function getFosOauthServer_Authorize_FormService()
    {
        return $this->services['fos_oauth_server.authorize.form'] = $this->get('form.factory')->createNamed('fos_oauth_server_authorize_form', 'fos_oauth_server_authorize', NULL, array('validation_groups' => array(0 => 'Authorize', 1 => 'Default')));
    }
    protected function getFosOauthServer_Authorize_Form_Handler_DefaultService()
    {
        if (!isset($this->scopedServices['request'])) {
            throw new InactiveScopeException('fos_oauth_server.authorize.form.handler.default', 'request');
        }
        return $this->services['fos_oauth_server.authorize.form.handler.default'] = $this->scopedServices['request']['fos_oauth_server.authorize.form.handler.default'] = new \FOS\OAuthServerBundle\Form\Handler\AuthorizeFormHandler($this->get('fos_oauth_server.authorize.form'), $this->get('request'));
    }
    protected function getFosOauthServer_Authorize_Form_TypeService()
    {
        return $this->services['fos_oauth_server.authorize.form.type'] = new \FOS\OAuthServerBundle\Form\Type\AuthorizeFormType();
    }
    protected function getFosOauthServer_ClientManager_DefaultService()
    {
        return $this->services['fos_oauth_server.client_manager.default'] = new \FOS\OAuthServerBundle\Entity\ClientManager($this->get('fos_oauth_server.entity_manager'), 'Mautic\\ApiBundle\\Entity\\oAuth2\\Client');
    }
    protected function getFosOauthServer_Controller_TokenService()
    {
        return $this->services['fos_oauth_server.controller.token'] = new \FOS\OAuthServerBundle\Controller\TokenController($this->get('fos_oauth_server.server'));
    }
    protected function getFosOauthServer_RefreshTokenManager_DefaultService()
    {
        return $this->services['fos_oauth_server.refresh_token_manager.default'] = new \FOS\OAuthServerBundle\Entity\RefreshTokenManager($this->get('fos_oauth_server.entity_manager'), 'Mautic\\ApiBundle\\Entity\\oAuth2\\RefreshToken');
    }
    protected function getFosOauthServer_ServerService()
    {
        return $this->services['fos_oauth_server.server'] = new \OAuth2\OAuth2($this->get('fos_oauth_server.storage'), array('access_token_lifetime' => 3600, 'refresh_token_lifetime' => 1209600));
    }
    protected function getFosOauthServer_StorageService()
    {
        return $this->services['fos_oauth_server.storage'] = new \FOS\OAuthServerBundle\Storage\OAuthStorage($this->get('fos_oauth_server.client_manager.default'), $this->get('fos_oauth_server.access_token_manager.default'), $this->get('fos_oauth_server.refresh_token_manager.default'), $this->get('fos_oauth_server.auth_code_manager.default'), $this->get('mautic.user.provider', ContainerInterface::NULL_ON_INVALID_REFERENCE), $this->get('security.encoder_factory'));
    }
    protected function getFosRest_BodyListenerService()
    {
        return $this->services['fos_rest.body_listener'] = new \FOS\RestBundle\EventListener\BodyListener($this->get('fos_rest.decoder_provider'), false);
    }
    protected function getFosRest_Decoder_JsonService()
    {
        return $this->services['fos_rest.decoder.json'] = new \FOS\RestBundle\Decoder\JsonDecoder();
    }
    protected function getFosRest_Decoder_JsontoformService()
    {
        return $this->services['fos_rest.decoder.jsontoform'] = new \FOS\RestBundle\Decoder\JsonToFormDecoder();
    }
    protected function getFosRest_Decoder_XmlService()
    {
        return $this->services['fos_rest.decoder.xml'] = new \FOS\RestBundle\Decoder\XmlDecoder();
    }
    protected function getFosRest_DecoderProviderService()
    {
        $this->services['fos_rest.decoder_provider'] = $instance = new \FOS\RestBundle\Decoder\ContainerDecoderProvider(array('json' => 'fos_rest.decoder.json', 'xml' => 'fos_rest.decoder.xml'));
        $instance->setContainer($this);
        return $instance;
    }
    protected function getFosRest_Form_Extension_CsrfDisableService()
    {
        return $this->services['fos_rest.form.extension.csrf_disable'] = new \FOS\RestBundle\Form\Extension\DisableCSRFExtension($this->get('security.context'), 'ROLE_API');
    }
    protected function getFosRest_FormatNegotiatorService()
    {
        return $this->services['fos_rest.format_negotiator'] = new \FOS\RestBundle\Util\FormatNegotiator();
    }
    protected function getFosRest_Inflector_DoctrineService()
    {
        return $this->services['fos_rest.inflector.doctrine'] = new \FOS\RestBundle\Util\Inflector\DoctrineInflector();
    }
    protected function getFosRest_Request_ParamFetcherService()
    {
        if (!isset($this->scopedServices['request'])) {
            throw new InactiveScopeException('fos_rest.request.param_fetcher', 'request');
        }
        return $this->services['fos_rest.request.param_fetcher'] = $this->scopedServices['request']['fos_rest.request.param_fetcher'] = new \FOS\RestBundle\Request\ParamFetcher($this->get('fos_rest.request.param_fetcher.reader'), $this->get('request'), $this->get('validator'));
    }
    protected function getFosRest_Request_ParamFetcher_ReaderService()
    {
        return $this->services['fos_rest.request.param_fetcher.reader'] = new \FOS\RestBundle\Request\ParamReader($this->get('annotation_reader'));
    }
    protected function getFosRest_Routing_Loader_ControllerService()
    {
        return $this->services['fos_rest.routing.loader.controller'] = new \FOS\RestBundle\Routing\Loader\RestRouteLoader($this, $this->get('file_locator'), $this->get('controller_name_converter'), $this->get('fos_rest.routing.loader.reader.controller'), 'json');
    }
    protected function getFosRest_Routing_Loader_ProcessorService()
    {
        return $this->services['fos_rest.routing.loader.processor'] = new \FOS\RestBundle\Routing\Loader\RestRouteProcessor();
    }
    protected function getFosRest_Routing_Loader_Reader_ActionService()
    {
        return $this->services['fos_rest.routing.loader.reader.action'] = new \FOS\RestBundle\Routing\Loader\Reader\RestActionReader($this->get('annotation_reader'), $this->get('fos_rest.request.param_fetcher.reader'), $this->get('fos_rest.inflector.doctrine'), false, array('json' => false));
    }
    protected function getFosRest_Routing_Loader_Reader_ControllerService()
    {
        return $this->services['fos_rest.routing.loader.reader.controller'] = new \FOS\RestBundle\Routing\Loader\Reader\RestControllerReader($this->get('fos_rest.routing.loader.reader.action'), $this->get('annotation_reader'));
    }
    protected function getFosRest_Routing_Loader_XmlCollectionService()
    {
        return $this->services['fos_rest.routing.loader.xml_collection'] = new \FOS\RestBundle\Routing\Loader\RestXmlCollectionLoader($this->get('file_locator'), $this->get('fos_rest.routing.loader.processor'), false, array('json' => false), 'json');
    }
    protected function getFosRest_Routing_Loader_YamlCollectionService()
    {
        return $this->services['fos_rest.routing.loader.yaml_collection'] = new \FOS\RestBundle\Routing\Loader\RestYamlCollectionLoader($this->get('file_locator'), $this->get('fos_rest.routing.loader.processor'), false, array('json' => false), 'json');
    }
    protected function getFosRest_SerializerService()
    {
        $a = new \JMS\Serializer\EventDispatcher\LazyEventDispatcher($this);
        $a->setListeners(array('serializer.pre_serialize' => array(0 => array(0 => array(0 => 'jms_serializer.doctrine_proxy_subscriber', 1 => 'onPreSerialize'), 1 => NULL, 2 => NULL))));
        return $this->services['fos_rest.serializer'] = new \JMS\Serializer\Serializer(new \Metadata\MetadataFactory(new \Metadata\Driver\LazyLoadingDriver($this, 'jms_serializer.metadata_driver'), 'Metadata\\ClassHierarchyMetadata', false), $this->get('jms_serializer.handler_registry'), $this->get('jms_serializer.unserialize_object_constructor'), new \PhpCollection\Map(array('json' => $this->get('jms_serializer.json_serialization_visitor'), 'xml' => $this->get('jms_serializer.xml_serialization_visitor'), 'yml' => $this->get('jms_serializer.yaml_serialization_visitor'))), new \PhpCollection\Map(array('json' => $this->get('jms_serializer.json_deserialization_visitor'), 'xml' => $this->get('jms_serializer.xml_deserialization_visitor'))), $a);
    }
    protected function getFosRest_View_ExceptionWrapperHandlerService()
    {
        return $this->services['fos_rest.view.exception_wrapper_handler'] = new \FOS\RestBundle\View\ExceptionWrapperHandler();
    }
    protected function getFosRest_ViewHandlerService()
    {
        $this->services['fos_rest.view_handler'] = $instance = new \FOS\RestBundle\View\ViewHandler(array('json' => false), 400, 204, false, array('html' => 302), 'twig');
        $instance->setExclusionStrategyGroups('');
        $instance->setExclusionStrategyVersion('');
        $instance->setSerializeNullStrategy(false);
        $instance->setContainer($this);
        return $instance;
    }
    protected function getFragment_HandlerService()
    {
        $this->services['fragment.handler'] = $instance = new \Symfony\Component\HttpKernel\Fragment\FragmentHandler(array(), false, $this->get('request_stack'));
        $instance->addRenderer($this->get('fragment.renderer.inline'));
        $instance->addRenderer($this->get('fragment.renderer.hinclude'));
        $instance->addRenderer($this->get('fragment.renderer.esi'));
        return $instance;
    }
    protected function getFragment_ListenerService()
    {
        return $this->services['fragment.listener'] = new \Symfony\Component\HttpKernel\EventListener\FragmentListener($this->get('uri_signer'), '/_fragment');
    }
    protected function getFragment_Renderer_EsiService()
    {
        $this->services['fragment.renderer.esi'] = $instance = new \Symfony\Component\HttpKernel\Fragment\EsiFragmentRenderer(NULL, $this->get('fragment.renderer.inline'), $this->get('uri_signer'));
        $instance->setFragmentPath('/_fragment');
        return $instance;
    }
    protected function getFragment_Renderer_HincludeService()
    {
        $this->services['fragment.renderer.hinclude'] = $instance = new \Symfony\Bundle\FrameworkBundle\Fragment\ContainerAwareHIncludeFragmentRenderer($this, $this->get('uri_signer'), NULL);
        $instance->setFragmentPath('/_fragment');
        return $instance;
    }
    protected function getFragment_Renderer_InlineService()
    {
        $this->services['fragment.renderer.inline'] = $instance = new \Symfony\Component\HttpKernel\Fragment\InlineFragmentRenderer($this->get('http_kernel'), $this->get('event_dispatcher'));
        $instance->setFragmentPath('/_fragment');
        return $instance;
    }
    protected function getHttpKernelService()
    {
        return $this->services['http_kernel'] = new \Symfony\Component\HttpKernel\DependencyInjection\ContainerAwareHttpKernel($this->get('event_dispatcher'), $this, new \Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver($this, $this->get('controller_name_converter'), $this->get('monolog.logger.request', ContainerInterface::NULL_ON_INVALID_REFERENCE)), $this->get('request_stack'));
    }
    protected function getJmsSerializer_ArrayCollectionHandlerService()
    {
        return $this->services['jms_serializer.array_collection_handler'] = new \JMS\Serializer\Handler\ArrayCollectionHandler();
    }
    protected function getJmsSerializer_ConstraintViolationHandlerService()
    {
        return $this->services['jms_serializer.constraint_violation_handler'] = new \JMS\Serializer\Handler\ConstraintViolationHandler();
    }
    protected function getJmsSerializer_DatetimeHandlerService()
    {
        return $this->services['jms_serializer.datetime_handler'] = new \JMS\Serializer\Handler\DateHandler('c', 'UTC', true);
    }
    protected function getJmsSerializer_DoctrineProxySubscriberService()
    {
        return $this->services['jms_serializer.doctrine_proxy_subscriber'] = new \JMS\Serializer\EventDispatcher\Subscriber\DoctrineProxySubscriber();
    }
    protected function getJmsSerializer_FormErrorHandlerService()
    {
        return $this->services['jms_serializer.form_error_handler'] = new \JMS\Serializer\Handler\FormErrorHandler($this->get('translator.default'));
    }
    protected function getJmsSerializer_HandlerRegistryService()
    {
        return $this->services['jms_serializer.handler_registry'] = new \JMS\Serializer\Handler\LazyHandlerRegistry($this, array(2 => array('DateTime' => array('json' => array(0 => 'jms_serializer.datetime_handler', 1 => 'deserializeDateTimeFromjson'), 'xml' => array(0 => 'jms_serializer.datetime_handler', 1 => 'deserializeDateTimeFromxml'), 'yml' => array(0 => 'jms_serializer.datetime_handler', 1 => 'deserializeDateTimeFromyml')), 'ArrayCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection')), 'Doctrine\\Common\\Collections\\ArrayCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection')), 'Doctrine\\ORM\\PersistentCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection')), 'Doctrine\\ODM\\MongoDB\\PersistentCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection')), 'Doctrine\\ODM\\PHPCR\\PersistentCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'deserializeCollection')), 'PhpCollection\\Sequence' => array('json' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'deserializeSequence'), 'xml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'deserializeSequence'), 'yml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'deserializeSequence')), 'PhpCollection\\Map' => array('json' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'deserializeMap'), 'xml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'deserializeMap'), 'yml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'deserializeMap'))), 1 => array('DateTime' => array('json' => array(0 => 'jms_serializer.datetime_handler', 1 => 'serializeDateTime'), 'xml' => array(0 => 'jms_serializer.datetime_handler', 1 => 'serializeDateTime'), 'yml' => array(0 => 'jms_serializer.datetime_handler', 1 => 'serializeDateTime')), 'DateInterval' => array('json' => array(0 => 'jms_serializer.datetime_handler', 1 => 'serializeDateInterval'), 'xml' => array(0 => 'jms_serializer.datetime_handler', 1 => 'serializeDateInterval'), 'yml' => array(0 => 'jms_serializer.datetime_handler', 1 => 'serializeDateInterval')), 'ArrayCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection')), 'Doctrine\\Common\\Collections\\ArrayCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection')), 'Doctrine\\ORM\\PersistentCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection')), 'Doctrine\\ODM\\MongoDB\\PersistentCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection')), 'Doctrine\\ODM\\PHPCR\\PersistentCollection' => array('json' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'xml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection'), 'yml' => array(0 => 'jms_serializer.array_collection_handler', 1 => 'serializeCollection')), 'PhpCollection\\Sequence' => array('json' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'serializeSequence'), 'xml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'serializeSequence'), 'yml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'serializeSequence')), 'PhpCollection\\Map' => array('json' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'serializeMap'), 'xml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'serializeMap'), 'yml' => array(0 => 'jms_serializer.php_collection_handler', 1 => 'serializeMap')), 'Symfony\\Component\\Form\\Form' => array('xml' => array(0 => 'jms_serializer.form_error_handler', 1 => 'serializeFormToxml'), 'json' => array(0 => 'jms_serializer.form_error_handler', 1 => 'serializeFormTojson'), 'yml' => array(0 => 'jms_serializer.form_error_handler', 1 => 'serializeFormToyml')), 'Symfony\\Component\\Form\\FormError' => array('xml' => array(0 => 'jms_serializer.form_error_handler', 1 => 'serializeFormErrorToxml'), 'json' => array(0 => 'jms_serializer.form_error_handler', 1 => 'serializeFormErrorTojson'), 'yml' => array(0 => 'jms_serializer.form_error_handler', 1 => 'serializeFormErrorToyml')), 'Symfony\\Component\\Validator\\ConstraintViolationList' => array('xml' => array(0 => 'jms_serializer.constraint_violation_handler', 1 => 'serializeListToxml'), 'json' => array(0 => 'jms_serializer.constraint_violation_handler', 1 => 'serializeListTojson'), 'yml' => array(0 => 'jms_serializer.constraint_violation_handler', 1 => 'serializeListToyml')), 'Symfony\\Component\\Validator\\ConstraintViolation' => array('xml' => array(0 => 'jms_serializer.constraint_violation_handler', 1 => 'serializeViolationToxml'), 'json' => array(0 => 'jms_serializer.constraint_violation_handler', 1 => 'serializeViolationTojson'), 'yml' => array(0 => 'jms_serializer.constraint_violation_handler', 1 => 'serializeViolationToyml')))));
    }
    protected function getJmsSerializer_JsonDeserializationVisitorService()
    {
        return $this->services['jms_serializer.json_deserialization_visitor'] = new \JMS\Serializer\JsonDeserializationVisitor($this->get('jms_serializer.naming_strategy'), $this->get('jms_serializer.unserialize_object_constructor'));
    }
    protected function getJmsSerializer_JsonSerializationVisitorService()
    {
        $this->services['jms_serializer.json_serialization_visitor'] = $instance = new \JMS\Serializer\JsonSerializationVisitor($this->get('jms_serializer.naming_strategy'));
        $instance->setOptions(0);
        return $instance;
    }
    protected function getJmsSerializer_MetadataDriverService()
    {
        $a = new \Metadata\Driver\FileLocator(array('Mautic\\FormBundle\\Entity' => ($this->targetDirs[2].'/bundles/FormBundle/Entity'), 'Mautic\\CategoryBundle\\Entity' => ($this->targetDirs[2].'/bundles/CategoryBundle/Entity'), 'Mautic\\LeadBundle\\Entity' => ($this->targetDirs[2].'/bundles/LeadBundle/Entity'), 'Mautic\\ReportBundle\\Entity' => ($this->targetDirs[2].'/bundles/ReportBundle/Entity'), 'Mautic\\PageBundle\\Entity' => ($this->targetDirs[2].'/bundles/PageBundle/Entity'), 'Mautic\\CampaignBundle\\Entity' => ($this->targetDirs[2].'/bundles/CampaignBundle/Entity'), 'Mautic\\PointBundle\\Entity' => ($this->targetDirs[2].'/bundles/PointBundle/Entity'), 'Mautic\\UserBundle\\Entity' => ($this->targetDirs[2].'/bundles/UserBundle/Entity'), 'Mautic\\EmailBundle\\Entity' => ($this->targetDirs[2].'/bundles/EmailBundle/Entity'), 'Mautic\\WebhookBundle\\Entity' => ($this->targetDirs[2].'/bundles/WebhookBundle/Entity'), 'Mautic\\CoreBundle\\Entity' => ($this->targetDirs[2].'/bundles/CoreBundle/Entity'), 'Mautic\\AssetBundle\\Entity' => ($this->targetDirs[2].'/bundles/AssetBundle/Entity')));
        return $this->services['jms_serializer.metadata_driver'] = new \JMS\Serializer\Metadata\Driver\DoctrineTypeDriver(new \Metadata\Driver\DriverChain(array(0 => new \JMS\Serializer\Metadata\Driver\YamlDriver($a), 1 => new \JMS\Serializer\Metadata\Driver\XmlDriver($a), 2 => new \Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver($a), 3 => new \Mautic\ApiBundle\Serializer\Driver\AnnotationDriver($this->get('annotation_reader')))), $this->get('doctrine'));
    }
    protected function getJmsSerializer_NamingStrategyService()
    {
        return $this->services['jms_serializer.naming_strategy'] = new \JMS\Serializer\Naming\CacheNamingStrategy(new \JMS\Serializer\Naming\SerializedNameAnnotationStrategy(new \JMS\Serializer\Naming\IdenticalPropertyNamingStrategy('', false)));
    }
    protected function getJmsSerializer_ObjectConstructorService()
    {
        return $this->services['jms_serializer.object_constructor'] = new \JMS\Serializer\Construction\DoctrineObjectConstructor($this->get('doctrine'), $this->get('jms_serializer.unserialize_object_constructor'));
    }
    protected function getJmsSerializer_PhpCollectionHandlerService()
    {
        return $this->services['jms_serializer.php_collection_handler'] = new \JMS\Serializer\Handler\PhpCollectionHandler();
    }
    protected function getJmsSerializer_Templating_Helper_SerializerService()
    {
        return $this->services['jms_serializer.templating.helper.serializer'] = new \JMS\SerializerBundle\Templating\SerializerHelper($this->get('fos_rest.serializer'));
    }
    protected function getJmsSerializer_XmlDeserializationVisitorService()
    {
        $this->services['jms_serializer.xml_deserialization_visitor'] = $instance = new \JMS\Serializer\XmlDeserializationVisitor($this->get('jms_serializer.naming_strategy'), $this->get('jms_serializer.unserialize_object_constructor'));
        $instance->setDoctypeWhitelist(array());
        return $instance;
    }
    protected function getJmsSerializer_XmlSerializationVisitorService()
    {
        return $this->services['jms_serializer.xml_serialization_visitor'] = new \JMS\Serializer\XmlSerializationVisitor($this->get('jms_serializer.naming_strategy'));
    }
    protected function getJmsSerializer_YamlSerializationVisitorService()
    {
        return $this->services['jms_serializer.yaml_serialization_visitor'] = new \JMS\Serializer\YamlSerializationVisitor($this->get('jms_serializer.naming_strategy'));
    }
    protected function getKernelService()
    {
        throw new RuntimeException('You have requested a synthetic service ("kernel"). The DIC does not know how to construct this service.');
    }
    protected function getKnpMenu_FactoryService()
    {
        $this->services['knp_menu.factory'] = $instance = new \Knp\Menu\MenuFactory();
        $instance->addExtension(new \Knp\Menu\Integration\Symfony\RoutingExtension($this->get('router')), 0);
        return $instance;
    }
    protected function getKnpMenu_Listener_VotersService()
    {
        $this->services['knp_menu.listener.voters'] = $instance = new \Knp\Bundle\MenuBundle\EventListener\VoterInitializerListener();
        $instance->addVoter($this->get('knp_menu.voter.router'));
        return $instance;
    }
    protected function getKnpMenu_MatcherService()
    {
        $this->services['knp_menu.matcher'] = $instance = new \Knp\Menu\Matcher\Matcher();
        $instance->addVoter($this->get('knp_menu.voter.router'));
        return $instance;
    }
    protected function getKnpMenu_MenuProviderService()
    {
        return $this->services['knp_menu.menu_provider'] = new \Knp\Menu\Provider\ChainProvider(array(0 => new \Knp\Bundle\MenuBundle\Provider\ContainerAwareProvider($this, array('main' => 'mautic.menu.main', 'admin' => 'mautic.menu.admin')), 1 => new \Knp\Bundle\MenuBundle\Provider\BuilderAliasProvider($this->get('kernel'), $this, $this->get('knp_menu.factory'))));
    }
    protected function getKnpMenu_Renderer_ListService()
    {
        return $this->services['knp_menu.renderer.list'] = new \Knp\Menu\Renderer\ListRenderer($this->get('knp_menu.matcher'), array(), 'UTF-8');
    }
    protected function getKnpMenu_RendererProviderService()
    {
        return $this->services['knp_menu.renderer_provider'] = new \Knp\Bundle\MenuBundle\Renderer\ContainerAwareProvider($this, 'mautic', array('list' => 'knp_menu.renderer.list', 'mautic' => 'mautic.menu_renderer'));
    }
    protected function getKnpMenu_Templating_HelperService()
    {
        return $this->services['knp_menu.templating.helper'] = new \Knp\Bundle\MenuBundle\Templating\Helper\MenuHelper(new \Knp\Menu\Twig\Helper($this->get('knp_menu.renderer_provider'), $this->get('knp_menu.menu_provider')));
    }
    protected function getKnpMenu_Voter_RouterService()
    {
        return $this->services['knp_menu.voter.router'] = new \Knp\Menu\Matcher\Voter\RouteVoter();
    }
    protected function getLocaleListenerService()
    {
        return $this->services['locale_listener'] = new \Symfony\Component\HttpKernel\EventListener\LocaleListener('ja', $this->get('router', ContainerInterface::NULL_ON_INVALID_REFERENCE), $this->get('request_stack'));
    }
    protected function getLoggerService()
    {
        $this->services['logger'] = $instance = new \Symfony\Bridge\Monolog\Logger('app');
        $instance->pushHandler($this->get('monolog.handler.main'));
        return $instance;
    }
    protected function getMautic_Api_Configbundle_SubscriberService()
    {
        return $this->services['mautic.api.configbundle.subscriber'] = new \Mautic\ApiBundle\EventListener\ConfigSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Api_Oauth_EventListenerService()
    {
        return $this->services['mautic.api.oauth.event_listener'] = new \Mautic\ApiBundle\EventListener\OAuthEventListener($this->get('mautic.factory'));
    }
    protected function getMautic_Api_Oauth1_NonceProviderService()
    {
        return $this->services['mautic.api.oauth1.nonce_provider'] = new \Mautic\ApiBundle\Provider\NonceProvider($this->get('doctrine.orm.default_entity_manager'));
    }
    protected function getMautic_Api_Search_SubscriberService()
    {
        return $this->services['mautic.api.search.subscriber'] = new \Mautic\ApiBundle\EventListener\SearchSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Api_SubscriberService()
    {
        return $this->services['mautic.api.subscriber'] = new \Mautic\ApiBundle\EventListener\ApiSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Asset_Builder_SubscriberService()
    {
        return $this->services['mautic.asset.builder.subscriber'] = new \Mautic\AssetBundle\EventListener\BuilderSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Asset_Campaignbundle_SubscriberService()
    {
        return $this->services['mautic.asset.campaignbundle.subscriber'] = new \Mautic\AssetBundle\EventListener\CampaignSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Asset_Configbundle_SubscriberService()
    {
        return $this->services['mautic.asset.configbundle.subscriber'] = new \Mautic\AssetBundle\EventListener\ConfigSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Asset_Emailbundle_SubscriberService()
    {
        return $this->services['mautic.asset.emailbundle.subscriber'] = new \Mautic\AssetBundle\EventListener\EmailSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Asset_Formbundle_SubscriberService()
    {
        return $this->services['mautic.asset.formbundle.subscriber'] = new \Mautic\AssetBundle\EventListener\FormSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Asset_Leadbundle_SubscriberService()
    {
        return $this->services['mautic.asset.leadbundle.subscriber'] = new \Mautic\AssetBundle\EventListener\LeadSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Asset_Pagebundle_SubscriberService()
    {
        return $this->services['mautic.asset.pagebundle.subscriber'] = new \Mautic\AssetBundle\EventListener\PageSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Asset_Pointbundle_SubscriberService()
    {
        return $this->services['mautic.asset.pointbundle.subscriber'] = new \Mautic\AssetBundle\EventListener\PointSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Asset_Reportbundle_SubscriberService()
    {
        return $this->services['mautic.asset.reportbundle.subscriber'] = new \Mautic\AssetBundle\EventListener\ReportSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Asset_Search_SubscriberService()
    {
        return $this->services['mautic.asset.search.subscriber'] = new \Mautic\AssetBundle\EventListener\SearchSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Asset_SubscriberService()
    {
        return $this->services['mautic.asset.subscriber'] = new \Mautic\AssetBundle\EventListener\AssetSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Asset_Upload_Error_HandlerService()
    {
        return $this->services['mautic.asset.upload.error.handler'] = new \Mautic\AssetBundle\ErrorHandler\DropzoneErrorHandler($this->get('mautic.factory'));
    }
    protected function getMautic_Campaign_Calendarbundle_SubscriberService()
    {
        return $this->services['mautic.campaign.calendarbundle.subscriber'] = new \Mautic\CampaignBundle\EventListener\CalendarSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Campaign_Leadbundle_SubscriberService()
    {
        return $this->services['mautic.campaign.leadbundle.subscriber'] = new \Mautic\CampaignBundle\EventListener\LeadSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Campaign_Pointbundle_SubscriberService()
    {
        return $this->services['mautic.campaign.pointbundle.subscriber'] = new \Mautic\CampaignBundle\EventListener\PointSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Campaign_Search_SubscriberService()
    {
        return $this->services['mautic.campaign.search.subscriber'] = new \Mautic\CampaignBundle\EventListener\SearchSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Campaign_SubscriberService()
    {
        return $this->services['mautic.campaign.subscriber'] = new \Mautic\CampaignBundle\EventListener\CampaignSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Campaign_Type_Action_AddremoveleadService()
    {
        return $this->services['mautic.campaign.type.action.addremovelead'] = new \Mautic\CampaignBundle\Form\Type\CampaignEventAddRemoveLeadType();
    }
    protected function getMautic_Campaign_Type_CampaignlistService()
    {
        return $this->services['mautic.campaign.type.campaignlist'] = new \Mautic\CampaignBundle\Form\Type\CampaignListType($this->get('mautic.factory'));
    }
    protected function getMautic_Campaign_Type_CanvassettingsService()
    {
        return $this->services['mautic.campaign.type.canvassettings'] = new \Mautic\CampaignBundle\Form\Type\EventCanvasSettingsType();
    }
    protected function getMautic_Campaign_Type_FormService()
    {
        return $this->services['mautic.campaign.type.form'] = new \Mautic\CampaignBundle\Form\Type\CampaignType($this->get('mautic.factory'));
    }
    protected function getMautic_Campaign_Type_LeadsourceService()
    {
        return $this->services['mautic.campaign.type.leadsource'] = new \Mautic\CampaignBundle\Form\Type\CampaignLeadSourceType($this->get('mautic.factory'));
    }
    protected function getMautic_Campaign_Type_Trigger_LeadchangeService()
    {
        return $this->services['mautic.campaign.type.trigger.leadchange'] = new \Mautic\CampaignBundle\Form\Type\CampaignEventLeadChangeType();
    }
    protected function getMautic_Campaignrange_Type_ActionService()
    {
        return $this->services['mautic.campaignrange.type.action'] = new \Mautic\CampaignBundle\Form\Type\EventType();
    }
    protected function getMautic_Category_SubscriberService()
    {
        return $this->services['mautic.category.subscriber'] = new \Mautic\CategoryBundle\EventListener\CategorySubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Cloudstorage_Remoteassetbrowse_SubscriberService()
    {
        return $this->services['mautic.cloudstorage.remoteassetbrowse.subscriber'] = new \MauticPlugin\MauticCloudStorageBundle\EventListener\RemoteAssetBrowseSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Config_SubscriberService()
    {
        return $this->services['mautic.config.subscriber'] = new \Mautic\ConfigBundle\EventListener\ConfigSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_ConfiguratorService()
    {
        return $this->services['mautic.configurator'] = new \Mautic\InstallBundle\Configurator\Configurator($this->targetDirs[2], $this->get('mautic.factory'));
    }
    protected function getMautic_Core_Auditlog_SubscriberService()
    {
        return $this->services['mautic.core.auditlog.subscriber'] = new \Mautic\CoreBundle\EventListener\AuditLogSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Core_Configbundle_SubscriberService()
    {
        return $this->services['mautic.core.configbundle.subscriber'] = new \Mautic\CoreBundle\EventListener\ConfigSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Core_Errorhandler_SubscriberService()
    {
        return $this->services['mautic.core.errorhandler.subscriber'] = new \Mautic\CoreBundle\EventListener\ErrorHandlingListener('prod', $this->get('monolog.logger.mautic'));
    }
    protected function getMautic_Core_SubscriberService()
    {
        return $this->services['mautic.core.subscriber'] = new \Mautic\CoreBundle\EventListener\CoreSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Email_Calendarbundle_SubscriberService()
    {
        return $this->services['mautic.email.calendarbundle.subscriber'] = new \Mautic\EmailBundle\EventListener\CalendarSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Email_Campaignbundle_SubscriberService()
    {
        return $this->services['mautic.email.campaignbundle.subscriber'] = new \Mautic\EmailBundle\EventListener\CampaignSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Email_Configbundle_SubscriberService()
    {
        return $this->services['mautic.email.configbundle.subscriber'] = new \Mautic\EmailBundle\EventListener\ConfigSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Email_Formbundle_SubscriberService()
    {
        return $this->services['mautic.email.formbundle.subscriber'] = new \Mautic\EmailBundle\EventListener\FormSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Email_Leadbundle_SubscriberService()
    {
        return $this->services['mautic.email.leadbundle.subscriber'] = new \Mautic\EmailBundle\EventListener\LeadSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Email_Pagebundle_SubscriberService()
    {
        return $this->services['mautic.email.pagebundle.subscriber'] = new \Mautic\EmailBundle\EventListener\PageSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Email_Pointbundle_SubscriberService()
    {
        return $this->services['mautic.email.pointbundle.subscriber'] = new \Mautic\EmailBundle\EventListener\PointSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Email_Reportbundle_SubscriberService()
    {
        return $this->services['mautic.email.reportbundle.subscriber'] = new \Mautic\EmailBundle\EventListener\ReportSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Email_Search_SubscriberService()
    {
        return $this->services['mautic.email.search.subscriber'] = new \Mautic\EmailBundle\EventListener\SearchSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Email_SubscriberService()
    {
        return $this->services['mautic.email.subscriber'] = new \Mautic\EmailBundle\EventListener\EmailSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Email_Type_BatchSendService()
    {
        return $this->services['mautic.email.type.batch_send'] = new \Mautic\EmailBundle\Form\Type\BatchSendType();
    }
    protected function getMautic_Email_Type_EmailAbtestSettingsService()
    {
        return $this->services['mautic.email.type.email_abtest_settings'] = new \Mautic\EmailBundle\Form\Type\AbTestPropertiesType();
    }
    protected function getMautic_Email_Webhook_SubscriberService()
    {
        return $this->services['mautic.email.webhook.subscriber'] = new \Mautic\EmailBundle\EventListener\WebhookSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Emailbuilder_SubscriberService()
    {
        return $this->services['mautic.emailbuilder.subscriber'] = new \Mautic\EmailBundle\EventListener\BuilderSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Exception_ListenerService()
    {
        return $this->services['mautic.exception.listener'] = new \Mautic\CoreBundle\EventListener\ExceptionListener('MauticCoreBundle:Exception:show', $this->get('monolog.logger.mautic'));
    }
    protected function getMautic_FactoryService()
    {
        return $this->services['mautic.factory'] = new \Mautic\CoreBundle\Factory\MauticFactory($this);
    }
    protected function getMautic_Form_Calendarbundle_SubscriberService()
    {
        return $this->services['mautic.form.calendarbundle.subscriber'] = new \Mautic\FormBundle\EventListener\CalendarSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Campaignbundle_SubscriberService()
    {
        return $this->services['mautic.form.campaignbundle.subscriber'] = new \Mautic\FormBundle\EventListener\CampaignSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Emailbundle_SubscriberService()
    {
        return $this->services['mautic.form.emailbundle.subscriber'] = new \Mautic\FormBundle\EventListener\EmailSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Leadbundle_SubscriberService()
    {
        return $this->services['mautic.form.leadbundle.subscriber'] = new \Mautic\FormBundle\EventListener\LeadSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Pagebundle_SubscriberService()
    {
        return $this->services['mautic.form.pagebundle.subscriber'] = new \Mautic\FormBundle\EventListener\PageSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Pointbundle_SubscriberService()
    {
        return $this->services['mautic.form.pointbundle.subscriber'] = new \Mautic\FormBundle\EventListener\PointSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Reportbundle_SubscriberService()
    {
        return $this->services['mautic.form.reportbundle.subscriber'] = new \Mautic\FormBundle\EventListener\ReportSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Search_SubscriberService()
    {
        return $this->services['mautic.form.search.subscriber'] = new \Mautic\FormBundle\EventListener\SearchSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Form_SubscriberService()
    {
        return $this->services['mautic.form.subscriber'] = new \Mautic\FormBundle\EventListener\FormSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_ActionService()
    {
        return $this->services['mautic.form.type.action'] = new \Mautic\FormBundle\Form\Type\ActionType();
    }
    protected function getMautic_Form_Type_ApiclientsService()
    {
        return $this->services['mautic.form.type.apiclients'] = new \Mautic\ApiBundle\Form\Type\ClientType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_ApiconfigService()
    {
        return $this->services['mautic.form.type.apiconfig'] = new \Mautic\ApiBundle\Form\Type\ConfigType();
    }
    protected function getMautic_Form_Type_AssetService()
    {
        return $this->services['mautic.form.type.asset'] = new \Mautic\AssetBundle\Form\Type\AssetType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_AssetconfigService()
    {
        return $this->services['mautic.form.type.assetconfig'] = new \Mautic\AssetBundle\Form\Type\ConfigType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_AssetlistService()
    {
        return $this->services['mautic.form.type.assetlist'] = new \Mautic\AssetBundle\Form\Type\AssetListType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_ButtonGroupService()
    {
        return $this->services['mautic.form.type.button_group'] = new \Mautic\CoreBundle\Form\Type\ButtonGroupType();
    }
    protected function getMautic_Form_Type_CampaigneventAssetdownloadService()
    {
        return $this->services['mautic.form.type.campaignevent_assetdownload'] = new \Mautic\AssetBundle\Form\Type\CampaignEventAssetDownloadType();
    }
    protected function getMautic_Form_Type_CampaigneventFormFieldValueService()
    {
        return $this->services['mautic.form.type.campaignevent_form_field_value'] = new \Mautic\FormBundle\Form\Type\CampaignEventFormFieldValueType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_CampaigneventFormsubmitService()
    {
        return $this->services['mautic.form.type.campaignevent_formsubmit'] = new \Mautic\FormBundle\Form\Type\CampaignEventFormSubmitType();
    }
    protected function getMautic_Form_Type_CampaigneventLeadFieldValueService()
    {
        return $this->services['mautic.form.type.campaignevent_lead_field_value'] = new \Mautic\LeadBundle\Form\Type\CampaignEventLeadFieldValueType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_CategoryService()
    {
        return $this->services['mautic.form.type.category'] = new \Mautic\CategoryBundle\Form\Type\CategoryListType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_CategoryBundlesFormService()
    {
        return $this->services['mautic.form.type.category_bundles_form'] = new \Mautic\CategoryBundle\Form\Type\CategoryBundlesType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_CategoryFormService()
    {
        return $this->services['mautic.form.type.category_form'] = new \Mautic\CategoryBundle\Form\Type\CategoryType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_Cloudstorage_OpenstackService()
    {
        return $this->services['mautic.form.type.cloudstorage.openstack'] = new \MauticPlugin\MauticCloudStorageBundle\Form\Type\OpenStackType();
    }
    protected function getMautic_Form_Type_Cloudstorage_RackspaceService()
    {
        return $this->services['mautic.form.type.cloudstorage.rackspace'] = new \MauticPlugin\MauticCloudStorageBundle\Form\Type\RackspaceType();
    }
    protected function getMautic_Form_Type_ConfigService()
    {
        return $this->services['mautic.form.type.config'] = new \Mautic\ConfigBundle\Form\Type\ConfigType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_CoreconfigService()
    {
        return $this->services['mautic.form.type.coreconfig'] = new \Mautic\CoreBundle\Form\Type\ConfigType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_CoreconfigMonitoredEmailService()
    {
        return $this->services['mautic.form.type.coreconfig_monitored_email'] = new \Mautic\EmailBundle\Form\Type\ConfigMonitoredEmailType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_CoreconfigMonitoredMailboxesService()
    {
        return $this->services['mautic.form.type.coreconfig_monitored_mailboxes'] = new \Mautic\EmailBundle\Form\Type\ConfigMonitoredMailboxesType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_DynamiclistService()
    {
        return $this->services['mautic.form.type.dynamiclist'] = new \Mautic\CoreBundle\Form\Type\DynamicListType();
    }
    protected function getMautic_Form_Type_EmailService()
    {
        return $this->services['mautic.form.type.email'] = new \Mautic\EmailBundle\Form\Type\EmailType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_EmailListService()
    {
        return $this->services['mautic.form.type.email_list'] = new \Mautic\EmailBundle\Form\Type\EmailListType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_EmailconfigService()
    {
        return $this->services['mautic.form.type.emailconfig'] = new \Mautic\EmailBundle\Form\Type\ConfigType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_Emailmarketing_ConstantcontactService()
    {
        return $this->services['mautic.form.type.emailmarketing.constantcontact'] = new \MauticPlugin\MauticEmailMarketingBundle\Form\Type\ConstantContactType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_Emailmarketing_IcontactService()
    {
        return $this->services['mautic.form.type.emailmarketing.icontact'] = new \MauticPlugin\MauticEmailMarketingBundle\Form\Type\IcontactType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_Emailmarketing_MailchimpService()
    {
        return $this->services['mautic.form.type.emailmarketing.mailchimp'] = new \MauticPlugin\MauticEmailMarketingBundle\Form\Type\MailchimpType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_EmailopenListService()
    {
        return $this->services['mautic.form.type.emailopen_list'] = new \Mautic\EmailBundle\Form\Type\EmailOpenType();
    }
    protected function getMautic_Form_Type_EmailsendListService()
    {
        return $this->services['mautic.form.type.emailsend_list'] = new \Mautic\EmailBundle\Form\Type\EmailSendType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_EmailvariantService()
    {
        return $this->services['mautic.form.type.emailvariant'] = new \Mautic\EmailBundle\Form\Type\VariantType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_FieldService()
    {
        return $this->services['mautic.form.type.field'] = new \Mautic\FormBundle\Form\Type\FieldType();
    }
    protected function getMautic_Form_Type_FieldPropertycaptchaService()
    {
        return $this->services['mautic.form.type.field_propertycaptcha'] = new \Mautic\FormBundle\Form\Type\FormFieldCaptchaType();
    }
    protected function getMautic_Form_Type_FieldPropertygroupService()
    {
        return $this->services['mautic.form.type.field_propertygroup'] = new \Mautic\FormBundle\Form\Type\FormFieldGroupType();
    }
    protected function getMautic_Form_Type_FieldPropertyplaceholderService()
    {
        return $this->services['mautic.form.type.field_propertyplaceholder'] = new \Mautic\FormBundle\Form\Type\FormFieldPlaceholderType();
    }
    protected function getMautic_Form_Type_FieldPropertyselectService()
    {
        return $this->services['mautic.form.type.field_propertyselect'] = new \Mautic\FormBundle\Form\Type\FormFieldSelectType();
    }
    protected function getMautic_Form_Type_FieldPropertytextService()
    {
        return $this->services['mautic.form.type.field_propertytext'] = new \Mautic\FormBundle\Form\Type\FormFieldTextType();
    }
    protected function getMautic_Form_Type_FilterSelectorService()
    {
        return $this->services['mautic.form.type.filter_selector'] = new \Mautic\ReportBundle\Form\Type\FilterSelectorType();
    }
    protected function getMautic_Form_Type_FormService()
    {
        return $this->services['mautic.form.type.form'] = new \Mautic\FormBundle\Form\Type\FormType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_FormButtonsService()
    {
        return $this->services['mautic.form.type.form_buttons'] = new \Mautic\CoreBundle\Form\Type\FormButtonsType();
    }
    protected function getMautic_Form_Type_FormListService()
    {
        return $this->services['mautic.form.type.form_list'] = new \Mautic\FormBundle\Form\Type\FormListType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_FormSubmitactionSendemailService()
    {
        return $this->services['mautic.form.type.form_submitaction_sendemail'] = new \Mautic\FormBundle\Form\Type\SubmitActionEmailType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_FormsubmitAssetdownloadService()
    {
        return $this->services['mautic.form.type.formsubmit_assetdownload'] = new \Mautic\AssetBundle\Form\Type\FormSubmitActionDownloadFileType();
    }
    protected function getMautic_Form_Type_FormsubmitSendemailAdminService()
    {
        return $this->services['mautic.form.type.formsubmit_sendemail_admin'] = new \Mautic\EmailBundle\Form\Type\FormSubmitActionUserEmailType();
    }
    protected function getMautic_Form_Type_HiddenEntityService()
    {
        return $this->services['mautic.form.type.hidden_entity'] = new \Mautic\CoreBundle\Form\Type\HiddenEntityType($this->get('doctrine.orm.default_entity_manager'));
    }
    protected function getMautic_Form_Type_Integration_ConfigService()
    {
        return $this->services['mautic.form.type.integration.config'] = new \Mautic\PluginBundle\Form\Type\IntegrationConfigType();
    }
    protected function getMautic_Form_Type_Integration_DetailsService()
    {
        return $this->services['mautic.form.type.integration.details'] = new \Mautic\PluginBundle\Form\Type\DetailsType();
    }
    protected function getMautic_Form_Type_Integration_FieldsService()
    {
        return $this->services['mautic.form.type.integration.fields'] = new \Mautic\PluginBundle\Form\Type\FieldsType();
    }
    protected function getMautic_Form_Type_Integration_KeysService()
    {
        return $this->services['mautic.form.type.integration.keys'] = new \Mautic\PluginBundle\Form\Type\KeysType();
    }
    protected function getMautic_Form_Type_Integration_ListService()
    {
        return $this->services['mautic.form.type.integration.list'] = new \Mautic\PluginBundle\Form\Type\IntegrationsListType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_Integration_SettingsService()
    {
        return $this->services['mautic.form.type.integration.settings'] = new \Mautic\PluginBundle\Form\Type\FeatureSettingsType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_LeadService()
    {
        return $this->services['mautic.form.type.lead'] = new \Mautic\LeadBundle\Form\Type\LeadType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_Lead_Submitaction_ChangelistService()
    {
        return $this->services['mautic.form.type.lead.submitaction.changelist'] = new \Mautic\LeadBundle\Form\Type\EventListType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_Lead_Submitaction_PointschangeService()
    {
        return $this->services['mautic.form.type.lead.submitaction.pointschange'] = new \Mautic\LeadBundle\Form\Type\FormSubmitActionPointsChangeType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_LeadBatchService()
    {
        return $this->services['mautic.form.type.lead_batch'] = new \Mautic\LeadBundle\Form\Type\BatchType();
    }
    protected function getMautic_Form_Type_LeadBatchDncService()
    {
        return $this->services['mautic.form.type.lead_batch_dnc'] = new \Mautic\LeadBundle\Form\Type\DncType();
    }
    protected function getMautic_Form_Type_LeadFieldImportService()
    {
        return $this->services['mautic.form.type.lead_field_import'] = new \Mautic\LeadBundle\Form\Type\LeadImportFieldType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_LeadFieldsService()
    {
        return $this->services['mautic.form.type.lead_fields'] = new \Mautic\LeadBundle\Form\Type\LeadFieldsType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_LeadImportService()
    {
        return $this->services['mautic.form.type.lead_import'] = new \Mautic\LeadBundle\Form\Type\LeadImportType();
    }
    protected function getMautic_Form_Type_LeadMergeService()
    {
        return $this->services['mautic.form.type.lead_merge'] = new \Mautic\LeadBundle\Form\Type\MergeType();
    }
    protected function getMautic_Form_Type_LeadQuickemailService()
    {
        return $this->services['mautic.form.type.lead_quickemail'] = new \Mautic\LeadBundle\Form\Type\EmailType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_LeadTagService()
    {
        return $this->services['mautic.form.type.lead_tag'] = new \Mautic\LeadBundle\Form\Type\TagType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_LeadTagsService()
    {
        return $this->services['mautic.form.type.lead_tags'] = new \Mautic\LeadBundle\Form\Type\TagListType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_LeadfieldService()
    {
        return $this->services['mautic.form.type.leadfield'] = new \Mautic\LeadBundle\Form\Type\FieldType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_LeadlistService()
    {
        return $this->services['mautic.form.type.leadlist'] = new \Mautic\LeadBundle\Form\Type\ListType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_LeadlistActionService()
    {
        return $this->services['mautic.form.type.leadlist_action'] = new \Mautic\LeadBundle\Form\Type\ListActionType();
    }
    protected function getMautic_Form_Type_LeadlistChoicesService()
    {
        return $this->services['mautic.form.type.leadlist_choices'] = new \Mautic\LeadBundle\Form\Type\LeadListType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_LeadlistFilterService()
    {
        return $this->services['mautic.form.type.leadlist_filter'] = new \Mautic\LeadBundle\Form\Type\FilterType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_LeadlistTriggerService()
    {
        return $this->services['mautic.form.type.leadlist_trigger'] = new \Mautic\LeadBundle\Form\Type\ListTriggerType();
    }
    protected function getMautic_Form_Type_LeadnoteService()
    {
        return $this->services['mautic.form.type.leadnote'] = new \Mautic\LeadBundle\Form\Type\NoteType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_LeadpointsActionService()
    {
        return $this->services['mautic.form.type.leadpoints_action'] = new \Mautic\LeadBundle\Form\Type\PointActionType();
    }
    protected function getMautic_Form_Type_LeadpointsTriggerService()
    {
        return $this->services['mautic.form.type.leadpoints_trigger'] = new \Mautic\LeadBundle\Form\Type\PointTriggerType();
    }
    protected function getMautic_Form_Type_ModifyLeadTagsService()
    {
        return $this->services['mautic.form.type.modify_lead_tags'] = new \Mautic\LeadBundle\Form\Type\ModifyLeadTagsType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_PageService()
    {
        return $this->services['mautic.form.type.page'] = new \Mautic\PageBundle\Form\Type\PageType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_PageAbtestSettingsService()
    {
        return $this->services['mautic.form.type.page_abtest_settings'] = new \Mautic\PageBundle\Form\Type\AbTestPropertiesType();
    }
    protected function getMautic_Form_Type_PagePublishDatesService()
    {
        return $this->services['mautic.form.type.page_publish_dates'] = new \Mautic\PageBundle\Form\Type\PagePublishDatesType();
    }
    protected function getMautic_Form_Type_PageconfigService()
    {
        return $this->services['mautic.form.type.pageconfig'] = new \Mautic\PageBundle\Form\Type\ConfigType();
    }
    protected function getMautic_Form_Type_Pagehit_CampaignTriggerService()
    {
        return $this->services['mautic.form.type.pagehit.campaign_trigger'] = new \Mautic\PageBundle\Form\Type\CampaignEventPageHitType();
    }
    protected function getMautic_Form_Type_PagelistService()
    {
        return $this->services['mautic.form.type.pagelist'] = new \Mautic\PageBundle\Form\Type\PageListType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_PagevariantService()
    {
        return $this->services['mautic.form.type.pagevariant'] = new \Mautic\PageBundle\Form\Type\VariantType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_PasswordresetService()
    {
        return $this->services['mautic.form.type.passwordreset'] = new \Mautic\UserBundle\Form\Type\PasswordResetType();
    }
    protected function getMautic_Form_Type_PermissionlistService()
    {
        return $this->services['mautic.form.type.permissionlist'] = new \Mautic\UserBundle\Form\Type\PermissionListType();
    }
    protected function getMautic_Form_Type_PermissionsService()
    {
        return $this->services['mautic.form.type.permissions'] = new \Mautic\UserBundle\Form\Type\PermissionsType();
    }
    protected function getMautic_Form_Type_PointactionAssetdownloadService()
    {
        return $this->services['mautic.form.type.pointaction_assetdownload'] = new \Mautic\AssetBundle\Form\Type\PointActionAssetDownloadType();
    }
    protected function getMautic_Form_Type_PointactionFormsubmitService()
    {
        return $this->services['mautic.form.type.pointaction_formsubmit'] = new \Mautic\FormBundle\Form\Type\PointActionFormSubmitType();
    }
    protected function getMautic_Form_Type_PointactionPointhitService()
    {
        return $this->services['mautic.form.type.pointaction_pointhit'] = new \Mautic\PageBundle\Form\Type\PointActionPageHitType();
    }
    protected function getMautic_Form_Type_PointactionUrlhitService()
    {
        return $this->services['mautic.form.type.pointaction_urlhit'] = new \Mautic\PageBundle\Form\Type\PointActionUrlHitType();
    }
    protected function getMautic_Form_Type_RedirectListService()
    {
        return $this->services['mautic.form.type.redirect_list'] = new \Mautic\PageBundle\Form\Type\RedirectListType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_ReportService()
    {
        return $this->services['mautic.form.type.report'] = new \Mautic\ReportBundle\Form\Type\ReportType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_ReportFiltersService()
    {
        return $this->services['mautic.form.type.report_filters'] = new \Mautic\ReportBundle\Form\Type\ReportFiltersType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_RoleService()
    {
        return $this->services['mautic.form.type.role'] = new \Mautic\UserBundle\Form\Type\RoleType();
    }
    protected function getMautic_Form_Type_SlideshowConfigService()
    {
        return $this->services['mautic.form.type.slideshow_config'] = new \Mautic\PageBundle\Form\Type\SlideshowGlobalConfigType();
    }
    protected function getMautic_Form_Type_SlideshowSlideConfigService()
    {
        return $this->services['mautic.form.type.slideshow_slide_config'] = new \Mautic\PageBundle\Form\Type\SlideshowSlideConfigType();
    }
    protected function getMautic_Form_Type_Social_FacebookService()
    {
        return $this->services['mautic.form.type.social.facebook'] = new \MauticPlugin\MauticSocialBundle\Form\Type\FacebookType();
    }
    protected function getMautic_Form_Type_Social_GoogleplusService()
    {
        return $this->services['mautic.form.type.social.googleplus'] = new \MauticPlugin\MauticSocialBundle\Form\Type\GooglePlusType();
    }
    protected function getMautic_Form_Type_Social_LinkedinService()
    {
        return $this->services['mautic.form.type.social.linkedin'] = new \MauticPlugin\MauticSocialBundle\Form\Type\LinkedInType();
    }
    protected function getMautic_Form_Type_Social_TwitterService()
    {
        return $this->services['mautic.form.type.social.twitter'] = new \MauticPlugin\MauticSocialBundle\Form\Type\TwitterType();
    }
    protected function getMautic_Form_Type_SortablelistService()
    {
        return $this->services['mautic.form.type.sortablelist'] = new \Mautic\CoreBundle\Form\Type\SortableListType();
    }
    protected function getMautic_Form_Type_SpacerService()
    {
        return $this->services['mautic.form.type.spacer'] = new \Mautic\CoreBundle\Form\Type\SpacerType();
    }
    protected function getMautic_Form_Type_StandaloneButtonService()
    {
        return $this->services['mautic.form.type.standalone_button'] = new \Mautic\CoreBundle\Form\Type\StandAloneButtonType();
    }
    protected function getMautic_Form_Type_TableOrderService()
    {
        return $this->services['mautic.form.type.table_order'] = new \Mautic\ReportBundle\Form\Type\TableOrderType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_TelService()
    {
        return $this->services['mautic.form.type.tel'] = new \Mautic\CoreBundle\Form\Type\TelType();
    }
    protected function getMautic_Form_Type_ThemeListService()
    {
        return $this->services['mautic.form.type.theme_list'] = new \Mautic\CoreBundle\Form\Type\ThemeListType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_UpdateleadActionService()
    {
        return $this->services['mautic.form.type.updatelead_action'] = new \Mautic\LeadBundle\Form\Type\UpdateLeadActionType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_UserService()
    {
        return $this->services['mautic.form.type.user'] = new \Mautic\UserBundle\Form\Type\UserType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_UserListService()
    {
        return $this->services['mautic.form.type.user_list'] = new \Mautic\UserBundle\Form\Type\UserListType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_WebhookService()
    {
        return $this->services['mautic.form.type.webhook'] = new \Mautic\WebhookBundle\Form\Type\WebhookType($this->get('mautic.factory'));
    }
    protected function getMautic_Form_Type_WebhookconfigService()
    {
        return $this->services['mautic.form.type.webhookconfig'] = new \Mautic\WebhookBundle\Form\Type\ConfigType();
    }
    protected function getMautic_Form_Type_YesnoButtonGroupService()
    {
        return $this->services['mautic.form.type.yesno_button_group'] = new \Mautic\CoreBundle\Form\Type\YesNoButtonGroupType();
    }
    protected function getMautic_Form_Webhook_SubscriberService()
    {
        return $this->services['mautic.form.webhook.subscriber'] = new \Mautic\FormBundle\EventListener\WebhookSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_AssetgenerationService()
    {
        return $this->services['mautic.helper.assetgeneration'] = new \Mautic\CoreBundle\Helper\AssetGenerationHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_CacheService()
    {
        return $this->services['mautic.helper.cache'] = new \Mautic\CoreBundle\Helper\CacheHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_CookieService()
    {
        return $this->services['mautic.helper.cookie'] = new \Mautic\CoreBundle\Helper\CookieHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_EncryptionService()
    {
        return $this->services['mautic.helper.encryption'] = new \Mautic\CoreBundle\Helper\EncryptionHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_IntegrationService()
    {
        return $this->services['mautic.helper.integration'] = new \Mautic\PluginBundle\Helper\IntegrationHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_LanguageService()
    {
        return $this->services['mautic.helper.language'] = new \Mautic\CoreBundle\Helper\LanguageHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_MailboxService()
    {
        return $this->services['mautic.helper.mailbox'] = new \Mautic\EmailBundle\MonitoredEmail\Mailbox($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_MenuService()
    {
        return $this->services['mautic.helper.menu'] = new \Mautic\CoreBundle\Menu\MenuHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_MessageService()
    {
        return $this->services['mautic.helper.message'] = new \Mautic\EmailBundle\Helper\MessageHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_Template_AnalyticsService()
    {
        return $this->services['mautic.helper.template.analytics'] = new \Mautic\CoreBundle\Templating\Helper\AnalyticsHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_Template_AvatarService()
    {
        return $this->services['mautic.helper.template.avatar'] = new \Mautic\LeadBundle\Templating\Helper\AvatarHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_Template_ButtonService()
    {
        return $this->services['mautic.helper.template.button'] = new \Mautic\CoreBundle\Templating\Helper\ButtonHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_Template_CanvasService()
    {
        return $this->services['mautic.helper.template.canvas'] = new \Mautic\CoreBundle\Templating\Helper\SidebarCanvasHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_Template_DateService()
    {
        return $this->services['mautic.helper.template.date'] = new \Mautic\CoreBundle\Templating\Helper\DateHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_Template_ExceptionService()
    {
        return $this->services['mautic.helper.template.exception'] = new \Mautic\CoreBundle\Templating\Helper\ExceptionHelper($this->targetDirs[2]);
    }
    protected function getMautic_Helper_Template_FormatterService()
    {
        return $this->services['mautic.helper.template.formatter'] = new \Mautic\CoreBundle\Templating\Helper\FormatterHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_Template_GravatarService()
    {
        return $this->services['mautic.helper.template.gravatar'] = new \Mautic\CoreBundle\Templating\Helper\GravatarHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_Template_MautibotService()
    {
        return $this->services['mautic.helper.template.mautibot'] = new \Mautic\CoreBundle\Templating\Helper\MautibotHelper();
    }
    protected function getMautic_Helper_Template_SecurityService()
    {
        return $this->services['mautic.helper.template.security'] = new \Mautic\CoreBundle\Templating\Helper\SecurityHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_ThemeService()
    {
        return $this->services['mautic.helper.theme'] = new \Mautic\CoreBundle\Helper\ThemeHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Helper_UpdateService()
    {
        return $this->services['mautic.helper.update'] = new \Mautic\CoreBundle\Helper\UpdateHelper($this->get('mautic.factory'));
    }
    protected function getMautic_Lead_Calendarbundle_SubscriberService()
    {
        return $this->services['mautic.lead.calendarbundle.subscriber'] = new \Mautic\LeadBundle\EventListener\CalendarSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Lead_Campaignbundle_SubscriberService()
    {
        return $this->services['mautic.lead.campaignbundle.subscriber'] = new \Mautic\LeadBundle\EventListener\CampaignSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Lead_Constraint_AliasService()
    {
        return $this->services['mautic.lead.constraint.alias'] = new \Mautic\LeadBundle\Form\Validator\Constraints\UniqueUserAliasValidator($this->get('mautic.factory'));
    }
    protected function getMautic_Lead_Doctrine_SubscriberService()
    {
        return $this->services['mautic.lead.doctrine.subscriber'] = new \Mautic\LeadBundle\EventListener\DoctrineSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Lead_Emailbundle_SubscriberService()
    {
        return $this->services['mautic.lead.emailbundle.subscriber'] = new \Mautic\LeadBundle\EventListener\EmailSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Lead_Formbundle_SubscriberService()
    {
        return $this->services['mautic.lead.formbundle.subscriber'] = new \Mautic\LeadBundle\EventListener\FormSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Lead_Pointbundle_SubscriberService()
    {
        return $this->services['mautic.lead.pointbundle.subscriber'] = new \Mautic\LeadBundle\EventListener\PointSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Lead_Reportbundle_SubscriberService()
    {
        return $this->services['mautic.lead.reportbundle.subscriber'] = new \Mautic\LeadBundle\EventListener\ReportSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Lead_Search_SubscriberService()
    {
        return $this->services['mautic.lead.search.subscriber'] = new \Mautic\LeadBundle\EventListener\SearchSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Lead_SubscriberService()
    {
        return $this->services['mautic.lead.subscriber'] = new \Mautic\LeadBundle\EventListener\LeadSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Menu_AdminService()
    {
        return $this->services['mautic.menu.admin'] = $this->get('mautic.menu.builder')->adminMenu();
    }
    protected function getMautic_Menu_BuilderService()
    {
        return $this->services['mautic.menu.builder'] = new \Mautic\CoreBundle\Menu\MenuBuilder($this->get('knp_menu.factory'), $this->get('knp_menu.matcher'), $this->get('mautic.factory'));
    }
    protected function getMautic_Menu_MainService()
    {
        return $this->services['mautic.menu.main'] = $this->get('mautic.menu.builder')->mainMenu();
    }
    protected function getMautic_MenuRendererService()
    {
        return $this->services['mautic.menu_renderer'] = new \Mautic\CoreBundle\Menu\MenuRenderer($this->get('knp_menu.matcher'), $this->get('mautic.factory'), 'UTF-8');
    }
    protected function getMautic_Page_Calendarbundle_SubscriberService()
    {
        return $this->services['mautic.page.calendarbundle.subscriber'] = new \Mautic\PageBundle\EventListener\CalendarSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Page_Campaignbundle_SubscriberService()
    {
        return $this->services['mautic.page.campaignbundle.subscriber'] = new \Mautic\PageBundle\EventListener\CampaignSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Page_Configbundle_SubscriberService()
    {
        return $this->services['mautic.page.configbundle.subscriber'] = new \Mautic\PageBundle\EventListener\ConfigSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Page_Leadbundle_SubscriberService()
    {
        return $this->services['mautic.page.leadbundle.subscriber'] = new \Mautic\PageBundle\EventListener\LeadSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Page_Pointbundle_SubscriberService()
    {
        return $this->services['mautic.page.pointbundle.subscriber'] = new \Mautic\PageBundle\EventListener\PointSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Page_Reportbundle_SubscriberService()
    {
        return $this->services['mautic.page.reportbundle.subscriber'] = new \Mautic\PageBundle\EventListener\ReportSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Page_Search_SubscriberService()
    {
        return $this->services['mautic.page.search.subscriber'] = new \Mautic\PageBundle\EventListener\SearchSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Page_SubscriberService()
    {
        return $this->services['mautic.page.subscriber'] = new \Mautic\PageBundle\EventListener\PageSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Page_Webhook_SubscriberService()
    {
        return $this->services['mautic.page.webhook.subscriber'] = new \Mautic\PageBundle\EventListener\WebhookSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Pagebuilder_SubscriberService()
    {
        return $this->services['mautic.pagebuilder.subscriber'] = new \Mautic\PageBundle\EventListener\BuilderSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Permission_ManagerService()
    {
        return $this->services['mautic.permission.manager'] = $this->get('doctrine')->getManagerForClass('Mautic\\UserBundle\\Entity\\Permission');
    }
    protected function getMautic_Permission_RepositoryService()
    {
        return $this->services['mautic.permission.repository'] = $this->get('mautic.permission.manager')->getRepository('Mautic\\UserBundle\\Entity\\Permission');
    }
    protected function getMautic_Plugin_Campaignbundle_SubscriberService()
    {
        return $this->services['mautic.plugin.campaignbundle.subscriber'] = new \Mautic\PluginBundle\EventListener\CampaignSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Plugin_Formbundle_SubscriberService()
    {
        return $this->services['mautic.plugin.formbundle.subscriber'] = new \Mautic\PluginBundle\EventListener\FormSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Plugin_Pointbundle_SubscriberService()
    {
        return $this->services['mautic.plugin.pointbundle.subscriber'] = new \Mautic\PluginBundle\EventListener\PointSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Point_Leadbundle_SubscriberService()
    {
        return $this->services['mautic.point.leadbundle.subscriber'] = new \Mautic\PointBundle\EventListener\LeadSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Point_Search_SubscriberService()
    {
        return $this->services['mautic.point.search.subscriber'] = new \Mautic\PointBundle\EventListener\SearchSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Point_SubscriberService()
    {
        return $this->services['mautic.point.subscriber'] = new \Mautic\PointBundle\EventListener\PointSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Point_Type_ActionService()
    {
        return $this->services['mautic.point.type.action'] = new \Mautic\PointBundle\Form\Type\PointActionType();
    }
    protected function getMautic_Point_Type_FormService()
    {
        return $this->services['mautic.point.type.form'] = new \Mautic\PointBundle\Form\Type\PointType($this->get('mautic.factory'));
    }
    protected function getMautic_Point_Type_GenericpointSettingsService()
    {
        return $this->services['mautic.point.type.genericpoint_settings'] = new \Mautic\PointBundle\Form\Type\GenericPointSettingsType();
    }
    protected function getMautic_Pointtrigger_Type_ActionService()
    {
        return $this->services['mautic.pointtrigger.type.action'] = new \Mautic\PointBundle\Form\Type\TriggerEventType();
    }
    protected function getMautic_Pointtrigger_Type_FormService()
    {
        return $this->services['mautic.pointtrigger.type.form'] = new \Mautic\PointBundle\Form\Type\TriggerType($this->get('mautic.factory'));
    }
    protected function getMautic_Report_Report_SubscriberService()
    {
        return $this->services['mautic.report.report.subscriber'] = new \Mautic\ReportBundle\EventListener\ReportSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Report_Search_SubscriberService()
    {
        return $this->services['mautic.report.search.subscriber'] = new \Mautic\ReportBundle\EventListener\SearchSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_RouteLoaderService()
    {
        return $this->services['mautic.route_loader'] = new \Mautic\CoreBundle\Loader\RouteLoader($this->get('mautic.factory'));
    }
    protected function getMautic_SecurityService()
    {
        return $this->services['mautic.security'] = new \Mautic\CoreBundle\Security\Permissions\CorePermissions($this->get('mautic.factory'));
    }
    protected function getMautic_Security_AuthenticationHandlerService()
    {
        return $this->services['mautic.security.authentication_handler'] = new \Mautic\UserBundle\Security\Authentication\AuthenticationHandler($this->get('router'), $this->get('session'));
    }
    protected function getMautic_Security_LogoutHandlerService()
    {
        return $this->services['mautic.security.logout_handler'] = new \Mautic\UserBundle\Security\Authentication\LogoutHandler($this->get('mautic.factory'));
    }
    protected function getMautic_TblprefixSubscriberService()
    {
        return $this->services['mautic.tblprefix_subscriber'] = new \Mautic\CoreBundle\EventListener\DoctrineEventsSubscriber();
    }
    protected function getMautic_Templating_NameParserService()
    {
        return $this->services['mautic.templating.name_parser'] = new \Mautic\CoreBundle\Templating\TemplateNameParser($this->get('kernel'));
    }
    protected function getMautic_Translation_LoaderService()
    {
        return $this->services['mautic.translation.loader'] = new \Mautic\CoreBundle\Loader\TranslationLoader($this->get('mautic.factory'));
    }
    protected function getMautic_Transport_AmazonService()
    {
        $this->services['mautic.transport.amazon'] = $instance = new \Mautic\EmailBundle\Swiftmailer\Transport\AmazonTransport();
        $instance->setUsername(NULL);
        $instance->setPassword(NULL);
        return $instance;
    }
    protected function getMautic_Transport_MandrillService()
    {
        $this->services['mautic.transport.mandrill'] = $instance = new \Mautic\EmailBundle\Swiftmailer\Transport\MandrillTransport();
        $instance->setUsername(NULL);
        $instance->setPassword(NULL);
        $instance->setMauticFactory($this->get('mautic.factory'));
        return $instance;
    }
    protected function getMautic_Transport_PostmarkService()
    {
        $this->services['mautic.transport.postmark'] = $instance = new \Mautic\EmailBundle\Swiftmailer\Transport\PostmarkTransport();
        $instance->setUsername(NULL);
        $instance->setPassword(NULL);
        return $instance;
    }
    protected function getMautic_Transport_SendgridService()
    {
        $this->services['mautic.transport.sendgrid'] = $instance = new \Mautic\EmailBundle\Swiftmailer\Transport\SendgridTransport();
        $instance->setUsername(NULL);
        $instance->setPassword(NULL);
        return $instance;
    }
    protected function getMautic_User_ManagerService()
    {
        return $this->services['mautic.user.manager'] = $this->get('doctrine')->getManagerForClass('Mautic\\UserBundle\\Entity\\User');
    }
    protected function getMautic_User_ProviderService()
    {
        return $this->services['mautic.user.provider'] = new \Mautic\UserBundle\Security\Provider\UserProvider($this->get('mautic.user.repository'), $this->get('mautic.permission.repository'), $this->get('session'));
    }
    protected function getMautic_User_RepositoryService()
    {
        return $this->services['mautic.user.repository'] = $this->get('mautic.user.manager')->getRepository('Mautic\\UserBundle\\Entity\\User');
    }
    protected function getMautic_User_Search_SubscriberService()
    {
        return $this->services['mautic.user.search.subscriber'] = new \Mautic\UserBundle\EventListener\SearchSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_User_SubscriberService()
    {
        return $this->services['mautic.user.subscriber'] = new \Mautic\UserBundle\EventListener\UserSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Validator_LeadlistaccessService()
    {
        return $this->services['mautic.validator.leadlistaccess'] = new \Mautic\LeadBundle\Form\Validator\Constraints\LeadListAccessValidator($this->get('mautic.factory'));
    }
    protected function getMautic_Validator_OauthcallbackService()
    {
        return $this->services['mautic.validator.oauthcallback'] = new \Mautic\ApiBundle\Form\Validator\Constraints\OAuthCallbackValidator();
    }
    protected function getMautic_Webhook_Audit_SubscriberService()
    {
        return $this->services['mautic.webhook.audit.subscriber'] = new \Mautic\WebhookBundle\EventListener\WebhookSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Webhook_Config_SubscriberService()
    {
        return $this->services['mautic.webhook.config.subscriber'] = new \Mautic\WebhookBundle\EventListener\ConfigSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Webhook_Email_SubscriberService()
    {
        return $this->services['mautic.webhook.email.subscriber'] = new \Mautic\WebhookBundle\EventListener\EmailSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Webhook_Form_SubscriberService()
    {
        return $this->services['mautic.webhook.form.subscriber'] = new \Mautic\WebhookBundle\EventListener\FormSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Webhook_Lead_SubscriberService()
    {
        return $this->services['mautic.webhook.lead.subscriber'] = new \Mautic\WebhookBundle\EventListener\LeadSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Webhook_Page_Hit_SubscriberService()
    {
        return $this->services['mautic.webhook.page.hit.subscriber'] = new \Mautic\WebhookBundle\EventListener\PageSubscriber($this->get('mautic.factory'));
    }
    protected function getMautic_Webhook_SubscriberService()
    {
        return $this->services['mautic.webhook.subscriber'] = new \Mautic\LeadBundle\EventListener\WebhookSubscriber($this->get('mautic.factory'));
    }
    protected function getMonolog_Handler_MainService()
    {
        return $this->services['monolog.handler.main'] = new \Monolog\Handler\FingersCrossedHandler($this->get('monolog.handler.nested'), 400, '200', true, true, NULL);
    }
    protected function getMonolog_Handler_MauticService()
    {
        return $this->services['monolog.handler.mautic'] = new \Monolog\Handler\RotatingFileHandler(($this->targetDirs[2].'/logs/mautic_prod.php'), 7, 250, true, NULL);
    }
    protected function getMonolog_Handler_NestedService()
    {
        return $this->services['monolog.handler.nested'] = new \Monolog\Handler\RotatingFileHandler(($this->targetDirs[2].'/logs/prod.php'), 7, 400, true, NULL);
    }
    protected function getMonolog_Logger_DoctrineService()
    {
        $this->services['monolog.logger.doctrine'] = $instance = new \Symfony\Bridge\Monolog\Logger('doctrine');
        $instance->pushHandler($this->get('monolog.handler.main'));
        return $instance;
    }
    protected function getMonolog_Logger_EmergencyService()
    {
        $this->services['monolog.logger.emergency'] = $instance = new \Symfony\Bridge\Monolog\Logger('emergency');
        $instance->pushHandler($this->get('monolog.handler.main'));
        return $instance;
    }
    protected function getMonolog_Logger_MauticService()
    {
        $this->services['monolog.logger.mautic'] = $instance = new \Symfony\Bridge\Monolog\Logger('mautic');
        $instance->pushHandler($this->get('monolog.handler.mautic'));
        return $instance;
    }
    protected function getMonolog_Logger_RequestService()
    {
        $this->services['monolog.logger.request'] = $instance = new \Symfony\Bridge\Monolog\Logger('request');
        $instance->pushHandler($this->get('monolog.handler.main'));
        return $instance;
    }
    protected function getMonolog_Logger_RouterService()
    {
        $this->services['monolog.logger.router'] = $instance = new \Symfony\Bridge\Monolog\Logger('router');
        $instance->pushHandler($this->get('monolog.handler.main'));
        return $instance;
    }
    protected function getMonolog_Logger_SecurityService()
    {
        $this->services['monolog.logger.security'] = $instance = new \Symfony\Bridge\Monolog\Logger('security');
        $instance->pushHandler($this->get('monolog.handler.main'));
        return $instance;
    }
    protected function getOneupUploader_ChunkManagerService()
    {
        return $this->services['oneup_uploader.chunk_manager'] = new \Oneup\UploaderBundle\Uploader\Chunk\ChunkManager(array('maxage' => 604800, 'storage' => array('type' => 'filesystem', 'filesystem' => NULL, 'directory' => (__DIR__.'/uploader/chunks'), 'stream_wrapper' => NULL, 'sync_buffer_size' => '100K', 'prefix' => 'chunks'), 'load_distribution' => true), $this->get('oneup_uploader.chunks_storage'));
    }
    protected function getOneupUploader_ChunksStorageService()
    {
        return $this->services['oneup_uploader.chunks_storage'] = new \Oneup\UploaderBundle\Uploader\Chunk\Storage\FilesystemStorage((__DIR__.'/uploader/chunks'));
    }
    protected function getOneupUploader_Controller_MauticService()
    {
        if (!isset($this->scopedServices['request'])) {
            throw new InactiveScopeException('oneup_uploader.controller.mautic', 'request');
        }
        return $this->services['oneup_uploader.controller.mautic'] = $this->scopedServices['request']['oneup_uploader.controller.mautic'] = new \Mautic\AssetBundle\Controller\UploadController($this, $this->get('oneup_uploader.storage.asset'), $this->get('mautic.asset.upload.error.handler'), array('error_handler' => 'mautic.asset.upload.error.handler', 'frontend' => 'custom', 'custom_frontend' => array('class' => 'Mautic\\AssetBundle\\Controller\\UploadController', 'name' => 'mautic'), 'storage' => array('directory' => ($this->targetDirs[2].'/../media/files'), 'service' => NULL, 'type' => 'filesystem', 'filesystem' => NULL, 'stream_wrapper' => NULL, 'sync_buffer_size' => '100K'), 'route_prefix' => '', 'allowed_mimetypes' => array(), 'disallowed_mimetypes' => array(), 'max_size' => 9223372036854775807, 'use_orphanage' => false, 'enable_progress' => false, 'enable_cancelation' => false, 'namer' => 'oneup_uploader.namer.uniqid'), 'asset');
    }
    protected function getOneupUploader_Namer_UniqidService()
    {
        return $this->services['oneup_uploader.namer.uniqid'] = new \Oneup\UploaderBundle\Uploader\Naming\UniqidNamer();
    }
    protected function getOneupUploader_OrphanageManagerService()
    {
        return $this->services['oneup_uploader.orphanage_manager'] = new \Oneup\UploaderBundle\Uploader\Orphanage\OrphanageManager($this, array('maxage' => 604800, 'directory' => (__DIR__.'/uploader/orphanage')));
    }
    protected function getOneupUploader_PreUploadService()
    {
        return $this->services['oneup_uploader.pre_upload'] = new \Mautic\AssetBundle\EventListener\UploadSubscriber($this->get('mautic.factory'));
    }
    protected function getOneupUploader_Routing_LoaderService()
    {
        return $this->services['oneup_uploader.routing.loader'] = new \Oneup\UploaderBundle\Routing\RouteLoader(array('asset' => array(0 => 'oneup_uploader.controller.mautic', 1 => array('enable_progress' => false, 'enable_cancelation' => false, 'route_prefix' => ''))));
    }
    protected function getOneupUploader_Storage_AssetService()
    {
        return $this->services['oneup_uploader.storage.asset'] = new \Oneup\UploaderBundle\Uploader\Storage\FilesystemStorage(($this->targetDirs[2].'/../media/files'));
    }
    protected function getOneupUploader_Templating_UploaderHelperService()
    {
        return $this->services['oneup_uploader.templating.uploader_helper'] = new \Oneup\UploaderBundle\Templating\Helper\UploaderHelper($this->get('router'), array('asset' => 8388608));
    }
    protected function getOneupUploader_Twig_Extension_UploaderService()
    {
        return $this->services['oneup_uploader.twig.extension.uploader'] = new \Oneup\UploaderBundle\Twig\Extension\UploaderExtension($this->get('oneup_uploader.templating.uploader_helper'));
    }
    protected function getOneupUploader_ValidationListener_AllowedMimetypeService()
    {
        return $this->services['oneup_uploader.validation_listener.allowed_mimetype'] = new \Oneup\UploaderBundle\EventListener\AllowedMimetypeValidationListener();
    }
    protected function getOneupUploader_ValidationListener_DisallowedMimetypeService()
    {
        return $this->services['oneup_uploader.validation_listener.disallowed_mimetype'] = new \Oneup\UploaderBundle\EventListener\DisallowedMimetypeValidationListener();
    }
    protected function getOneupUploader_ValidationListener_MaxSizeService()
    {
        return $this->services['oneup_uploader.validation_listener.max_size'] = new \Oneup\UploaderBundle\EventListener\MaxSizeValidationListener();
    }
    protected function getPropertyAccessorService()
    {
        return $this->services['property_accessor'] = new \Symfony\Component\PropertyAccess\PropertyAccessor();
    }
    protected function getRequestService()
    {
        if (!isset($this->scopedServices['request'])) {
            throw new InactiveScopeException('request', 'request');
        }
        throw new RuntimeException('You have requested a synthetic service ("request"). The DIC does not know how to construct this service.');
    }
    protected function getRequestStackService()
    {
        return $this->services['request_stack'] = new \Symfony\Component\HttpFoundation\RequestStack();
    }
    protected function getResponseListenerService()
    {
        return $this->services['response_listener'] = new \Symfony\Component\HttpKernel\EventListener\ResponseListener('UTF-8');
    }
    protected function getRouterService()
    {
        return $this->services['router'] = new \Symfony\Bundle\FrameworkBundle\Routing\Router($this, ($this->targetDirs[2].'/config/routing.php'), array('cache_dir' => __DIR__, 'debug' => false, 'generator_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator', 'generator_base_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator', 'generator_dumper_class' => 'Symfony\\Component\\Routing\\Generator\\Dumper\\PhpGeneratorDumper', 'generator_cache_class' => 'appProdUrlGenerator', 'matcher_class' => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\RedirectableUrlMatcher', 'matcher_base_class' => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\RedirectableUrlMatcher', 'matcher_dumper_class' => 'Symfony\\Component\\Routing\\Matcher\\Dumper\\PhpMatcherDumper', 'matcher_cache_class' => 'appProdUrlMatcher', 'strict_requirements' => NULL), $this->get('router.request_context', ContainerInterface::NULL_ON_INVALID_REFERENCE), $this->get('monolog.logger.router', ContainerInterface::NULL_ON_INVALID_REFERENCE));
    }
    protected function getRouterListenerService()
    {
        return $this->services['router_listener'] = new \Symfony\Component\HttpKernel\EventListener\RouterListener($this->get('router'), $this->get('router.request_context', ContainerInterface::NULL_ON_INVALID_REFERENCE), $this->get('monolog.logger.request', ContainerInterface::NULL_ON_INVALID_REFERENCE), $this->get('request_stack'));
    }
    protected function getRouting_LoaderService()
    {
        $a = $this->get('file_locator');
        $b = new \Symfony\Component\Config\Loader\LoaderResolver();
        $b->addLoader(new \Symfony\Component\Routing\Loader\XmlFileLoader($a));
        $b->addLoader(new \Symfony\Component\Routing\Loader\YamlFileLoader($a));
        $b->addLoader(new \Symfony\Component\Routing\Loader\PhpFileLoader($a));
        $b->addLoader($this->get('fos_rest.routing.loader.controller'));
        $b->addLoader($this->get('fos_rest.routing.loader.yaml_collection'));
        $b->addLoader($this->get('fos_rest.routing.loader.xml_collection'));
        $b->addLoader($this->get('oneup_uploader.routing.loader'));
        $b->addLoader($this->get('mautic.route_loader'));
        return $this->services['routing.loader'] = new \Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader($this->get('controller_name_converter'), $this->get('monolog.logger.router', ContainerInterface::NULL_ON_INVALID_REFERENCE), $b);
    }
    protected function getSecurity_AuthenticationUtilsService()
    {
        return $this->services['security.authentication_utils'] = new \Symfony\Component\Security\Http\Authentication\AuthenticationUtils($this->get('request_stack'));
    }
    protected function getSecurity_AuthorizationCheckerService()
    {
        return $this->services['security.authorization_checker'] = new \Symfony\Component\Security\Core\Authorization\AuthorizationChecker($this->get('security.token_storage'), $this->get('security.authentication.manager'), $this->get('security.access.decision_manager'), false);
    }
    protected function getSecurity_ContextService()
    {
        return $this->services['security.context'] = new \Symfony\Component\Security\Core\SecurityContext($this->get('security.token_storage'), $this->get('security.authorization_checker'));
    }
    protected function getSecurity_Csrf_TokenManagerService()
    {
        return $this->services['security.csrf.token_manager'] = new \Symfony\Component\Security\Csrf\CsrfTokenManager(new \Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator($this->get('security.secure_random')), new \Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage($this->get('session')));
    }
    protected function getSecurity_EncoderFactoryService()
    {
        return $this->services['security.encoder_factory'] = new \Symfony\Component\Security\Core\Encoder\EncoderFactory(array('Symfony\\Component\\Security\\Core\\User\\User' => array('class' => 'Symfony\\Component\\Security\\Core\\Encoder\\BCryptPasswordEncoder', 'arguments' => array(0 => 13)), 'Mautic\\UserBundle\\Entity\\User' => array('class' => 'Symfony\\Component\\Security\\Core\\Encoder\\BCryptPasswordEncoder', 'arguments' => array(0 => 13))));
    }
    protected function getSecurity_FirewallService()
    {
        return $this->services['security.firewall'] = new \Symfony\Component\Security\Http\Firewall(new \Symfony\Bundle\SecurityBundle\Security\FirewallMap($this, array('security.firewall.map.context.install' => new \Symfony\Component\HttpFoundation\RequestMatcher('^/installer'), 'security.firewall.map.context.dev' => new \Symfony\Component\HttpFoundation\RequestMatcher('^/(_(profiler|wdt)|css|images|js)/'), 'security.firewall.map.context.login' => new \Symfony\Component\HttpFoundation\RequestMatcher('^/s/login$'), 'security.firewall.map.context.oauth2_token' => new \Symfony\Component\HttpFoundation\RequestMatcher('^/oauth/v2/token'), 'security.firewall.map.context.oauth2_area' => new \Symfony\Component\HttpFoundation\RequestMatcher('^/oauth/v2/authorize'), 'security.firewall.map.context.oauth1_request_token' => new \Symfony\Component\HttpFoundation\RequestMatcher('^/oauth/v1/request_token'), 'security.firewall.map.context.oauth1_access_token' => new \Symfony\Component\HttpFoundation\RequestMatcher('^/oauth/v1/access_token'), 'security.firewall.map.context.oauth1_area' => new \Symfony\Component\HttpFoundation\RequestMatcher('^/oauth/v1/authorize'), 'security.firewall.map.context.api' => new \Symfony\Component\HttpFoundation\RequestMatcher('^/api'), 'security.firewall.map.context.main' => new \Symfony\Component\HttpFoundation\RequestMatcher('^/s/'), 'security.firewall.map.context.public' => new \Symfony\Component\HttpFoundation\RequestMatcher('^/'))), $this->get('event_dispatcher'));
    }
    protected function getSecurity_Firewall_Map_Context_ApiService()
    {
        $a = $this->get('security.context');
        $b = $this->get('security.authentication.manager');
        $c = $this->get('fos_oauth_server.server');
        $d = $this->get('mautic.factory');
        $e = new \Mautic\ApiBundle\Security\OAuth2\Firewall\OAuthListener($a, $b, $c);
        $e->setFactory($d);
        $f = new \Mautic\ApiBundle\Security\OAuth1\Firewall\OAuthListener($a, $b);
        $f->setFactory($d);
        return $this->services['security.firewall.map.context.api'] = new \Symfony\Bundle\SecurityBundle\Security\FirewallContext(array(0 => $this->get('security.channel_listener'), 1 => $e, 2 => $f, 3 => $this->get('security.access_listener')), new \Symfony\Component\Security\Http\Firewall\ExceptionListener($a, $this->get('security.authentication.trust_resolver'), $this->get('security.http_utils'), 'api', new \FOS\OAuthServerBundle\Security\EntryPoint\OAuthEntryPoint($c), NULL, NULL, $this->get('monolog.logger.security', ContainerInterface::NULL_ON_INVALID_REFERENCE)));
    }
    protected function getSecurity_Firewall_Map_Context_DevService()
    {
        $a = $this->get('security.context');
        $b = $this->get('monolog.logger.security', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        return $this->services['security.firewall.map.context.dev'] = new \Symfony\Bundle\SecurityBundle\Security\FirewallContext(array(0 => $this->get('security.channel_listener'), 1 => new \Symfony\Component\Security\Http\Firewall\ContextListener($a, array(0 => $this->get('mautic.user.provider')), 'dev', $b, $this->get('event_dispatcher', ContainerInterface::NULL_ON_INVALID_REFERENCE)), 2 => new \Symfony\Component\Security\Http\Firewall\AnonymousAuthenticationListener($a, '567aa2dbd72de8.36627330', $b, $this->get('security.authentication.manager')), 3 => $this->get('security.access_listener')), new \Symfony\Component\Security\Http\Firewall\ExceptionListener($a, $this->get('security.authentication.trust_resolver'), $this->get('security.http_utils'), 'dev', NULL, NULL, NULL, $b));
    }
    protected function getSecurity_Firewall_Map_Context_InstallService()
    {
        return $this->services['security.firewall.map.context.install'] = new \Symfony\Bundle\SecurityBundle\Security\FirewallContext(array(), NULL);
    }
    protected function getSecurity_Firewall_Map_Context_LoginService()
    {
        $a = $this->get('security.context');
        $b = $this->get('monolog.logger.security', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        return $this->services['security.firewall.map.context.login'] = new \Symfony\Bundle\SecurityBundle\Security\FirewallContext(array(0 => $this->get('security.channel_listener'), 1 => $this->get('security.context_listener.1'), 2 => new \Symfony\Component\Security\Http\Firewall\AnonymousAuthenticationListener($a, '567aa2dbd72de8.36627330', $b, $this->get('security.authentication.manager')), 3 => $this->get('security.access_listener')), new \Symfony\Component\Security\Http\Firewall\ExceptionListener($a, $this->get('security.authentication.trust_resolver'), $this->get('security.http_utils'), 'login', NULL, NULL, NULL, $b));
    }
    protected function getSecurity_Firewall_Map_Context_MainService()
    {
        $a = $this->get('security.http_utils');
        $b = $this->get('mautic.user.provider');
        $c = $this->get('monolog.logger.security', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $d = $this->get('security.context');
        $e = $this->get('mautic.security.authentication_handler');
        $f = $this->get('security.authentication.manager');
        $g = $this->get('event_dispatcher', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $h = new \Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices(array(0 => $b), 'ab90c625f80f8126309fec4fae1da7fff78001dd', 'main', array('lifetime' => '31536000', 'path' => '/', 'domain' => NULL, 'name' => 'REMEMBERME', 'secure' => false, 'httponly' => true, 'always_remember_me' => false, 'remember_me_parameter' => '_remember_me'), $c);
        $i = new \Symfony\Component\Security\Http\Firewall\LogoutListener($d, $a, new \Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler($a, '/s/login'), array('csrf_parameter' => '_csrf_token', 'intention' => 'logout', 'logout_path' => '/s/logout'));
        $i->addHandler(new \Symfony\Component\Security\Http\Logout\SessionLogoutHandler());
        $i->addHandler($this->get('mautic.security.logout_handler'));
        $i->addHandler($h);
        $j = new \Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener($d, $f, $this->get('security.authentication.session_strategy'), $a, 'main', new \Symfony\Component\Security\Http\Authentication\CustomAuthenticationSuccessHandler($e, array('login_path' => '/s/login', 'always_use_default_target_path' => false, 'default_target_path' => '/', 'target_path_parameter' => '_target_path', 'use_referer' => false), 'main'), new \Symfony\Component\Security\Http\Authentication\CustomAuthenticationFailureHandler($e, array('login_path' => '/s/login', 'failure_path' => NULL, 'failure_forward' => false, 'failure_path_parameter' => '_failure_path')), array('check_path' => '/s/login_check', 'use_forward' => false, 'require_previous_session' => true, 'username_parameter' => '_username', 'password_parameter' => '_password', 'csrf_parameter' => '_csrf_token', 'intention' => 'authenticate', 'post_only' => true), $c, $g, $this->get('form.csrf_provider'));
        $j->setRememberMeServices($h);
        return $this->services['security.firewall.map.context.main'] = new \Symfony\Bundle\SecurityBundle\Security\FirewallContext(array(0 => $this->get('security.channel_listener'), 1 => $this->get('security.context_listener.1'), 2 => $i, 3 => $j, 4 => new \Symfony\Component\Security\Http\Firewall\RememberMeListener($d, $h, $f, $c, $g, true), 5 => $this->get('security.access_listener')), new \Symfony\Component\Security\Http\Firewall\ExceptionListener($d, $this->get('security.authentication.trust_resolver'), $a, 'main', new \Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint($this->get('http_kernel'), $a, '/s/login', false), NULL, NULL, $c));
    }
    protected function getSecurity_Firewall_Map_Context_Oauth1AccessTokenService()
    {
        return $this->services['security.firewall.map.context.oauth1_access_token'] = new \Symfony\Bundle\SecurityBundle\Security\FirewallContext(array(), NULL);
    }
    protected function getSecurity_Firewall_Map_Context_Oauth1AreaService()
    {
        $a = $this->get('security.context');
        $b = $this->get('monolog.logger.security', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $c = $this->get('event_dispatcher', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $d = $this->get('security.http_utils');
        $e = $this->get('http_kernel');
        $f = $this->get('security.authentication.manager');
        $g = new \Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler($d, array());
        $g->setOptions(array('login_path' => '/oauth/v1/authorize_login', 'always_use_default_target_path' => false, 'default_target_path' => '/', 'target_path_parameter' => '_target_path', 'use_referer' => false));
        $g->setProviderKey('oauth1_area');
        $h = new \Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler($e, $d, array(), $b);
        $h->setOptions(array('login_path' => '/oauth/v1/authorize_login', 'failure_path' => NULL, 'failure_forward' => false, 'failure_path_parameter' => '_failure_path'));
        return $this->services['security.firewall.map.context.oauth1_area'] = new \Symfony\Bundle\SecurityBundle\Security\FirewallContext(array(0 => $this->get('security.channel_listener'), 1 => new \Symfony\Component\Security\Http\Firewall\ContextListener($a, array(0 => $this->get('mautic.user.provider')), 'oauth1_area', $b, $c), 2 => new \Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener($a, $f, $this->get('security.authentication.session_strategy'), $d, 'oauth1_area', $g, $h, array('check_path' => '/oauth/v1/authorize_login_check', 'use_forward' => false, 'require_previous_session' => true, 'username_parameter' => '_username', 'password_parameter' => '_password', 'csrf_parameter' => '_csrf_token', 'intention' => 'authenticate', 'post_only' => true), $b, $c, NULL), 3 => new \Symfony\Component\Security\Http\Firewall\AnonymousAuthenticationListener($a, '567aa2dbd72de8.36627330', $b, $f), 4 => $this->get('security.access_listener')), new \Symfony\Component\Security\Http\Firewall\ExceptionListener($a, $this->get('security.authentication.trust_resolver'), $d, 'oauth1_area', new \Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint($e, $d, '/oauth/v1/authorize_login', false), NULL, NULL, $b));
    }
    protected function getSecurity_Firewall_Map_Context_Oauth1RequestTokenService()
    {
        return $this->services['security.firewall.map.context.oauth1_request_token'] = new \Symfony\Bundle\SecurityBundle\Security\FirewallContext(array(), NULL);
    }
    protected function getSecurity_Firewall_Map_Context_Oauth2AreaService()
    {
        $a = $this->get('security.context');
        $b = $this->get('monolog.logger.security', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $c = $this->get('event_dispatcher', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $d = $this->get('security.http_utils');
        $e = $this->get('http_kernel');
        $f = $this->get('security.authentication.manager');
        $g = new \Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler($d, array());
        $g->setOptions(array('login_path' => '/oauth/v2/authorize_login', 'always_use_default_target_path' => false, 'default_target_path' => '/', 'target_path_parameter' => '_target_path', 'use_referer' => false));
        $g->setProviderKey('oauth2_area');
        $h = new \Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler($e, $d, array(), $b);
        $h->setOptions(array('login_path' => '/oauth/v2/authorize_login', 'failure_path' => NULL, 'failure_forward' => false, 'failure_path_parameter' => '_failure_path'));
        return $this->services['security.firewall.map.context.oauth2_area'] = new \Symfony\Bundle\SecurityBundle\Security\FirewallContext(array(0 => $this->get('security.channel_listener'), 1 => new \Symfony\Component\Security\Http\Firewall\ContextListener($a, array(0 => $this->get('mautic.user.provider')), 'oauth2_area', $b, $c), 2 => new \Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener($a, $f, $this->get('security.authentication.session_strategy'), $d, 'oauth2_area', $g, $h, array('check_path' => '/oauth/v2/authorize_login_check', 'use_forward' => false, 'require_previous_session' => true, 'username_parameter' => '_username', 'password_parameter' => '_password', 'csrf_parameter' => '_csrf_token', 'intention' => 'authenticate', 'post_only' => true), $b, $c, NULL), 3 => new \Symfony\Component\Security\Http\Firewall\AnonymousAuthenticationListener($a, '567aa2dbd72de8.36627330', $b, $f), 4 => $this->get('security.access_listener')), new \Symfony\Component\Security\Http\Firewall\ExceptionListener($a, $this->get('security.authentication.trust_resolver'), $d, 'oauth2_area', new \Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint($e, $d, '/oauth/v2/authorize_login', false), NULL, NULL, $b));
    }
    protected function getSecurity_Firewall_Map_Context_Oauth2TokenService()
    {
        return $this->services['security.firewall.map.context.oauth2_token'] = new \Symfony\Bundle\SecurityBundle\Security\FirewallContext(array(), NULL);
    }
    protected function getSecurity_Firewall_Map_Context_PublicService()
    {
        $a = $this->get('security.context');
        $b = $this->get('monolog.logger.security', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        return $this->services['security.firewall.map.context.public'] = new \Symfony\Bundle\SecurityBundle\Security\FirewallContext(array(0 => $this->get('security.channel_listener'), 1 => $this->get('security.context_listener.1'), 2 => new \Symfony\Component\Security\Http\Firewall\AnonymousAuthenticationListener($a, '567aa2dbd72de8.36627330', $b, $this->get('security.authentication.manager')), 3 => $this->get('security.access_listener')), new \Symfony\Component\Security\Http\Firewall\ExceptionListener($a, $this->get('security.authentication.trust_resolver'), $this->get('security.http_utils'), 'public', NULL, NULL, NULL, $b));
    }
    protected function getSecurity_PasswordEncoderService()
    {
        return $this->services['security.password_encoder'] = new \Symfony\Component\Security\Core\Encoder\UserPasswordEncoder($this->get('security.encoder_factory'));
    }
    protected function getSecurity_Rememberme_ResponseListenerService()
    {
        return $this->services['security.rememberme.response_listener'] = new \Symfony\Component\Security\Http\RememberMe\ResponseListener();
    }
    protected function getSecurity_SecureRandomService()
    {
        return $this->services['security.secure_random'] = new \Symfony\Component\Security\Core\Util\SecureRandom((__DIR__.'/secure_random.seed'), $this->get('monolog.logger.security', ContainerInterface::NULL_ON_INVALID_REFERENCE));
    }
    protected function getSecurity_TokenStorageService()
    {
        return $this->services['security.token_storage'] = new \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage();
    }
    protected function getSecurity_Validator_UserPasswordService()
    {
        return $this->services['security.validator.user_password'] = new \Symfony\Component\Security\Core\Validator\Constraints\UserPasswordValidator($this->get('security.context'), $this->get('security.encoder_factory'));
    }
    protected function getServiceContainerService()
    {
        throw new RuntimeException('You have requested a synthetic service ("service_container"). The DIC does not know how to construct this service.');
    }
    protected function getSessionService()
    {
        return $this->services['session'] = new \Symfony\Component\HttpFoundation\Session\Session($this->get('session.storage.native'), new \Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag(), new \Symfony\Component\HttpFoundation\Session\Flash\FlashBag());
    }
    protected function getSession_SaveListenerService()
    {
        return $this->services['session.save_listener'] = new \Symfony\Component\HttpKernel\EventListener\SaveSessionListener();
    }
    protected function getSession_Storage_FilesystemService()
    {
        return $this->services['session.storage.filesystem'] = new \Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage((__DIR__.'/sessions'), 'MOCKSESSID', $this->get('session.storage.metadata_bag'));
    }
    protected function getSession_Storage_NativeService()
    {
        return $this->services['session.storage.native'] = new \Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage(array('name' => 'fb383c994053a92e79536ebaf21cb996', 'gc_probability' => 1), NULL, $this->get('session.storage.metadata_bag'));
    }
    protected function getSession_Storage_PhpBridgeService()
    {
        return $this->services['session.storage.php_bridge'] = new \Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage(NULL, $this->get('session.storage.metadata_bag'));
    }
    protected function getSessionListenerService()
    {
        return $this->services['session_listener'] = new \Symfony\Bundle\FrameworkBundle\EventListener\SessionListener($this);
    }
    protected function getStreamedResponseListenerService()
    {
        return $this->services['streamed_response_listener'] = new \Symfony\Component\HttpKernel\EventListener\StreamedResponseListener();
    }
    protected function getSwiftmailer_EmailSender_ListenerService()
    {
        return $this->services['swiftmailer.email_sender.listener'] = new \Symfony\Bundle\SwiftmailerBundle\EventListener\EmailSenderListener($this, $this->get('logger', ContainerInterface::NULL_ON_INVALID_REFERENCE));
    }
    protected function getSwiftmailer_Mailer_DefaultService()
    {
        return $this->services['swiftmailer.mailer.default'] = new \Swift_Mailer($this->get('swiftmailer.mailer.default.transport'));
    }
    protected function getSwiftmailer_Mailer_Default_TransportService()
    {
        return $this->services['swiftmailer.mailer.default.transport'] = new \Swift_Transport_MailTransport(new \Swift_Transport_SimpleMailInvoker(), new \Swift_Events_SimpleEventDispatcher());
    }
    protected function getTemplatingService()
    {
        $this->services['templating'] = $instance = new \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine($this->get('templating.name_parser'), $this, $this->get('templating.loader'), $this->get('templating.globals'));
        $instance->setCharset('UTF-8');
        $instance->setHelpers(array('slots' => 'templating.helper.slots', 'assets' => 'templating.helper.assets', 'request' => 'templating.helper.request', 'session' => 'templating.helper.session', 'router' => 'templating.helper.router', 'actions' => 'templating.helper.actions', 'code' => 'templating.helper.code', 'translator' => 'templating.helper.translator', 'form' => 'templating.helper.form', 'stopwatch' => 'templating.helper.stopwatch', 'logout_url' => 'templating.helper.logout_url', 'security' => 'mautic.helper.template.security', 'knp_menu' => 'knp_menu.templating.helper', 'jms_serializer' => 'jms_serializer.templating.helper.serializer', 'oneup_uploader' => 'oneup_uploader.templating.uploader_helper', 'menu_helper' => 'mautic.helper.menu', 'date' => 'mautic.helper.template.date', 'exception' => 'mautic.helper.template.exception', 'gravatar' => 'mautic.helper.template.gravatar', 'analytics' => 'mautic.helper.template.analytics', 'mautibot' => 'mautic.helper.template.mautibot', 'canvas' => 'mautic.helper.template.canvas', 'buttons' => 'mautic.helper.template.button', 'formatter' => 'mautic.helper.template.formatter', 'lead_avatar' => 'mautic.helper.template.avatar'));
        return $instance;
    }
    protected function getTemplating_Asset_PackageFactoryService()
    {
        return $this->services['templating.asset.package_factory'] = new \Symfony\Bundle\FrameworkBundle\Templating\Asset\PackageFactory($this);
    }
    protected function getTemplating_FilenameParserService()
    {
        return $this->services['templating.filename_parser'] = new \Symfony\Bundle\FrameworkBundle\Templating\TemplateFilenameParser();
    }
    protected function getTemplating_GlobalsService()
    {
        return $this->services['templating.globals'] = new \Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables($this);
    }
    protected function getTemplating_Helper_ActionsService()
    {
        return $this->services['templating.helper.actions'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\ActionsHelper($this->get('fragment.handler'));
    }
    protected function getTemplating_Helper_AssetsService()
    {
        if (!isset($this->scopedServices['request'])) {
            throw new InactiveScopeException('templating.helper.assets', 'request');
        }
        $this->services['templating.helper.assets'] = $this->scopedServices['request']['templating.helper.assets'] = $instance = new \Mautic\CoreBundle\Templating\Helper\AssetsHelper(new \Symfony\Bundle\FrameworkBundle\Templating\Asset\PathPackage($this->get('request'), NULL, '%s?%s'), array());
        $instance->setFactory($this->get('mautic.factory'));
        $instance->setAssetHelper($this->get('mautic.helper.assetgeneration'));
        return $instance;
    }
    protected function getTemplating_Helper_CodeService()
    {
        return $this->services['templating.helper.code'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\CodeHelper(NULL, $this->targetDirs[2], 'UTF-8');
    }
    protected function getTemplating_Helper_FormService()
    {
        return $this->services['templating.helper.form'] = new \Mautic\CoreBundle\Templating\Helper\FormHelper(new \Symfony\Component\Form\FormRenderer(new \Symfony\Component\Form\Extension\Templating\TemplatingRendererEngine($this->get('templating'), array(0 => 'FrameworkBundle:Form', 1 => 'MauticCoreBundle:FormTheme\\Custom')), $this->get('form.csrf_provider', ContainerInterface::NULL_ON_INVALID_REFERENCE)));
    }
    protected function getTemplating_Helper_LogoutUrlService()
    {
        $this->services['templating.helper.logout_url'] = $instance = new \Symfony\Bundle\SecurityBundle\Templating\Helper\LogoutUrlHelper($this, $this->get('router'));
        $instance->registerListener('main', '/s/logout', 'logout', '_csrf_token', NULL);
        return $instance;
    }
    protected function getTemplating_Helper_RequestService()
    {
        return $this->services['templating.helper.request'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\RequestHelper($this->get('request_stack'));
    }
    protected function getTemplating_Helper_RouterService()
    {
        return $this->services['templating.helper.router'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper($this->get('router'));
    }
    protected function getTemplating_Helper_SecurityService()
    {
        return $this->services['templating.helper.security'] = new \Symfony\Bundle\SecurityBundle\Templating\Helper\SecurityHelper($this->get('security.context', ContainerInterface::NULL_ON_INVALID_REFERENCE));
    }
    protected function getTemplating_Helper_SessionService()
    {
        return $this->services['templating.helper.session'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\SessionHelper($this->get('request_stack'));
    }
    protected function getTemplating_Helper_SlotsService()
    {
        return $this->services['templating.helper.slots'] = new \Mautic\CoreBundle\Templating\Helper\SlotsHelper();
    }
    protected function getTemplating_Helper_StopwatchService()
    {
        return $this->services['templating.helper.stopwatch'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\StopwatchHelper(NULL);
    }
    protected function getTemplating_Helper_TranslatorService()
    {
        return $this->services['templating.helper.translator'] = new \Mautic\CoreBundle\Templating\Helper\TranslatorHelper($this->get('translator.default'));
    }
    protected function getTemplating_LoaderService()
    {
        return $this->services['templating.loader'] = new \Symfony\Bundle\FrameworkBundle\Templating\Loader\FilesystemLoader($this->get('templating.locator'));
    }
    protected function getTemplating_NameParserService()
    {
        return $this->services['templating.name_parser'] = new \Mautic\CoreBundle\Templating\TemplateNameParser($this->get('kernel'));
    }
    protected function getTransifexService()
    {
        return $this->services['transifex'] = new \BabDev\Transifex\Transifex(array('api.username' => '', 'api.password' => ''));
    }
    protected function getTranslation_Dumper_CsvService()
    {
        return $this->services['translation.dumper.csv'] = new \Symfony\Component\Translation\Dumper\CsvFileDumper();
    }
    protected function getTranslation_Dumper_IniService()
    {
        return $this->services['translation.dumper.ini'] = new \Symfony\Component\Translation\Dumper\IniFileDumper();
    }
    protected function getTranslation_Dumper_JsonService()
    {
        return $this->services['translation.dumper.json'] = new \Symfony\Component\Translation\Dumper\JsonFileDumper();
    }
    protected function getTranslation_Dumper_MoService()
    {
        return $this->services['translation.dumper.mo'] = new \Symfony\Component\Translation\Dumper\MoFileDumper();
    }
    protected function getTranslation_Dumper_PhpService()
    {
        return $this->services['translation.dumper.php'] = new \Symfony\Component\Translation\Dumper\PhpFileDumper();
    }
    protected function getTranslation_Dumper_PoService()
    {
        return $this->services['translation.dumper.po'] = new \Symfony\Component\Translation\Dumper\PoFileDumper();
    }
    protected function getTranslation_Dumper_QtService()
    {
        return $this->services['translation.dumper.qt'] = new \Symfony\Component\Translation\Dumper\QtFileDumper();
    }
    protected function getTranslation_Dumper_ResService()
    {
        return $this->services['translation.dumper.res'] = new \Symfony\Component\Translation\Dumper\IcuResFileDumper();
    }
    protected function getTranslation_Dumper_XliffService()
    {
        return $this->services['translation.dumper.xliff'] = new \Symfony\Component\Translation\Dumper\XliffFileDumper();
    }
    protected function getTranslation_Dumper_YmlService()
    {
        return $this->services['translation.dumper.yml'] = new \Symfony\Component\Translation\Dumper\YamlFileDumper();
    }
    protected function getTranslation_ExtractorService()
    {
        $this->services['translation.extractor'] = $instance = new \Symfony\Component\Translation\Extractor\ChainExtractor();
        $instance->addExtractor('php', $this->get('translation.extractor.php'));
        return $instance;
    }
    protected function getTranslation_Extractor_PhpService()
    {
        return $this->services['translation.extractor.php'] = new \Symfony\Bundle\FrameworkBundle\Translation\PhpExtractor();
    }
    protected function getTranslation_LoaderService()
    {
        $a = $this->get('translation.loader.xliff');
        $this->services['translation.loader'] = $instance = new \Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader();
        $instance->addLoader('php', $this->get('translation.loader.php'));
        $instance->addLoader('yml', $this->get('translation.loader.yml'));
        $instance->addLoader('xlf', $a);
        $instance->addLoader('xliff', $a);
        $instance->addLoader('po', $this->get('translation.loader.po'));
        $instance->addLoader('mo', $this->get('translation.loader.mo'));
        $instance->addLoader('ts', $this->get('translation.loader.qt'));
        $instance->addLoader('csv', $this->get('translation.loader.csv'));
        $instance->addLoader('res', $this->get('translation.loader.res'));
        $instance->addLoader('dat', $this->get('translation.loader.dat'));
        $instance->addLoader('ini', $this->get('translation.loader.ini'));
        $instance->addLoader('json', $this->get('translation.loader.json'));
        $instance->addLoader('mautic', $this->get('mautic.translation.loader'));
        return $instance;
    }
    protected function getTranslation_Loader_CsvService()
    {
        return $this->services['translation.loader.csv'] = new \Symfony\Component\Translation\Loader\CsvFileLoader();
    }
    protected function getTranslation_Loader_DatService()
    {
        return $this->services['translation.loader.dat'] = new \Symfony\Component\Translation\Loader\IcuDatFileLoader();
    }
    protected function getTranslation_Loader_IniService()
    {
        return $this->services['translation.loader.ini'] = new \Symfony\Component\Translation\Loader\IniFileLoader();
    }
    protected function getTranslation_Loader_JsonService()
    {
        return $this->services['translation.loader.json'] = new \Symfony\Component\Translation\Loader\JsonFileLoader();
    }
    protected function getTranslation_Loader_MoService()
    {
        return $this->services['translation.loader.mo'] = new \Symfony\Component\Translation\Loader\MoFileLoader();
    }
    protected function getTranslation_Loader_PhpService()
    {
        return $this->services['translation.loader.php'] = new \Symfony\Component\Translation\Loader\PhpFileLoader();
    }
    protected function getTranslation_Loader_PoService()
    {
        return $this->services['translation.loader.po'] = new \Symfony\Component\Translation\Loader\PoFileLoader();
    }
    protected function getTranslation_Loader_QtService()
    {
        return $this->services['translation.loader.qt'] = new \Symfony\Component\Translation\Loader\QtFileLoader();
    }
    protected function getTranslation_Loader_ResService()
    {
        return $this->services['translation.loader.res'] = new \Symfony\Component\Translation\Loader\IcuResFileLoader();
    }
    protected function getTranslation_Loader_XliffService()
    {
        return $this->services['translation.loader.xliff'] = new \Symfony\Component\Translation\Loader\XliffFileLoader();
    }
    protected function getTranslation_Loader_YmlService()
    {
        return $this->services['translation.loader.yml'] = new \Symfony\Component\Translation\Loader\YamlFileLoader();
    }
    protected function getTranslation_WriterService()
    {
        $this->services['translation.writer'] = $instance = new \Symfony\Component\Translation\Writer\TranslationWriter();
        $instance->addDumper('php', $this->get('translation.dumper.php'));
        $instance->addDumper('xlf', $this->get('translation.dumper.xliff'));
        $instance->addDumper('po', $this->get('translation.dumper.po'));
        $instance->addDumper('mo', $this->get('translation.dumper.mo'));
        $instance->addDumper('yml', $this->get('translation.dumper.yml'));
        $instance->addDumper('ts', $this->get('translation.dumper.qt'));
        $instance->addDumper('csv', $this->get('translation.dumper.csv'));
        $instance->addDumper('ini', $this->get('translation.dumper.ini'));
        $instance->addDumper('json', $this->get('translation.dumper.json'));
        $instance->addDumper('res', $this->get('translation.dumper.res'));
        return $instance;
    }
    protected function getTranslator_DefaultService()
    {
        $this->services['translator.default'] = $instance = new \Mautic\CoreBundle\Translation\Translator($this, new \Symfony\Component\Translation\MessageSelector(), array('translation.loader.php' => array(0 => 'php'), 'translation.loader.yml' => array(0 => 'yml'), 'translation.loader.xliff' => array(0 => 'xlf', 1 => 'xliff'), 'translation.loader.po' => array(0 => 'po'), 'translation.loader.mo' => array(0 => 'mo'), 'translation.loader.qt' => array(0 => 'ts'), 'translation.loader.csv' => array(0 => 'csv'), 'translation.loader.res' => array(0 => 'res'), 'translation.loader.dat' => array(0 => 'dat'), 'translation.loader.ini' => array(0 => 'ini'), 'translation.loader.json' => array(0 => 'json'), 'mautic.translation.loader' => array(0 => 'mautic')), array('cache_dir' => (__DIR__.'/translations'), 'debug' => false));
        $instance->setFallbackLocales(array(0 => 'en_US'));
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.vi.xlf'), 'vi', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.ar.xlf'), 'ar', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.sv.xlf'), 'sv', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.ca.xlf'), 'ca', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.sr_Cyrl.xlf'), 'sr_Cyrl', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.af.xlf'), 'af', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.eu.xlf'), 'eu', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.bg.xlf'), 'bg', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.cy.xlf'), 'cy', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.nb.xlf'), 'nb', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.zh_CN.xlf'), 'zh_CN', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.fa.xlf'), 'fa', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.lt.xlf'), 'lt', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.sl.xlf'), 'sl', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.it.xlf'), 'it', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.az.xlf'), 'az', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.id.xlf'), 'id', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.cs.xlf'), 'cs', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.en.xlf'), 'en', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.ja.xlf'), 'ja', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.fr.xlf'), 'fr', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.fi.xlf'), 'fi', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.pl.xlf'), 'pl', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.mn.xlf'), 'mn', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.uk.xlf'), 'uk', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.hy.xlf'), 'hy', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.et.xlf'), 'et', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.nl.xlf'), 'nl', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.el.xlf'), 'el', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.da.xlf'), 'da', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.gl.xlf'), 'gl', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.pt.xlf'), 'pt', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.es.xlf'), 'es', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.pt_BR.xlf'), 'pt_BR', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.sr_Latn.xlf'), 'sr_Latn', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.zh_TW.xlf'), 'zh_TW', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.no.xlf'), 'no', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.hr.xlf'), 'hr', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.sk.xlf'), 'sk', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.hu.xlf'), 'hu', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.sq.xlf'), 'sq', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.lb.xlf'), 'lb', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.th.xlf'), 'th', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.ru.xlf'), 'ru', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.tr.xlf'), 'tr', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.ro.xlf'), 'ro', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.de.xlf'), 'de', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.he.xlf'), 'he', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.ar.xlf'), 'ar', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.sv.xlf'), 'sv', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.ca.xlf'), 'ca', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.sr_Cyrl.xlf'), 'sr_Cyrl', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.eu.xlf'), 'eu', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.bg.xlf'), 'bg', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.nb.xlf'), 'nb', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.zh_CN.xlf'), 'zh_CN', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.fa.xlf'), 'fa', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.lt.xlf'), 'lt', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.sl.xlf'), 'sl', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.it.xlf'), 'it', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.az.xlf'), 'az', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.id.xlf'), 'id', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.cs.xlf'), 'cs', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.en.xlf'), 'en', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.ja.xlf'), 'ja', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.fr.xlf'), 'fr', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.fi.xlf'), 'fi', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.pl.xlf'), 'pl', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.lv.xlf'), 'lv', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.mn.xlf'), 'mn', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.uk.xlf'), 'uk', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.hy.xlf'), 'hy', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.et.xlf'), 'et', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.nl.xlf'), 'nl', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.el.xlf'), 'el', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.da.xlf'), 'da', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.gl.xlf'), 'gl', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.pt.xlf'), 'pt', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.es.xlf'), 'es', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.pt_BR.xlf'), 'pt_BR', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.sr_Latn.xlf'), 'sr_Latn', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.hr.xlf'), 'hr', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.sk.xlf'), 'sk', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.hu.xlf'), 'hu', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.lb.xlf'), 'lb', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.ru.xlf'), 'ru', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.ro.xlf'), 'ro', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.de.xlf'), 'de', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.he.xlf'), 'he', 'validators');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.fr.xlf'), 'fr', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.hr.xlf'), 'hr', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.vi.xlf'), 'vi', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.pl.xlf'), 'pl', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.ro.xlf'), 'ro', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.sr_Cyrl.xlf'), 'sr_Cyrl', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.sv.xlf'), 'sv', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.ja.xlf'), 'ja', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.pt_BR.xlf'), 'pt_BR', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.th.xlf'), 'th', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.id.xlf'), 'id', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.zh_CN.xlf'), 'zh_CN', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.lt.xlf'), 'lt', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.es.xlf'), 'es', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.az.xlf'), 'az', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.da.xlf'), 'da', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.no.xlf'), 'no', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.en.xlf'), 'en', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.sk.xlf'), 'sk', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.cs.xlf'), 'cs', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.ar.xlf'), 'ar', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.ua.xlf'), 'ua', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.sr_Latn.xlf'), 'sr_Latn', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.hu.xlf'), 'hu', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.bg.xlf'), 'bg', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.it.xlf'), 'it', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.ru.xlf'), 'ru', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.lb.xlf'), 'lb', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.fa.xlf'), 'fa', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.el.xlf'), 'el', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.he.xlf'), 'he', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.gl.xlf'), 'gl', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.ca.xlf'), 'ca', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.tr.xlf'), 'tr', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.de.xlf'), 'de', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.nl.xlf'), 'nl', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.sl.xlf'), 'sl', 'security');
        $instance->addResource('xlf', ($this->targetDirs[3].'/vendor/symfony/security/Symfony/Component/Security/Core/Exception/../Resources/translations/security.pt_PT.xlf'), 'pt_PT', 'security');
        $instance->addResource('yml', ($this->targetDirs[3].'/vendor/friendsofsymfony/oauth-server-bundle/FOS/OAuthServerBundle/Resources/translations/FOSOAuthServerBundle.sl.yml'), 'sl', 'FOSOAuthServerBundle');
        $instance->addResource('yml', ($this->targetDirs[3].'/vendor/friendsofsymfony/oauth-server-bundle/FOS/OAuthServerBundle/Resources/translations/FOSOAuthServerBundle.en.yml'), 'en', 'FOSOAuthServerBundle');
        $instance->addResource('yml', ($this->targetDirs[3].'/vendor/friendsofsymfony/oauth-server-bundle/FOS/OAuthServerBundle/Resources/translations/FOSOAuthServerBundle.fr.yml'), 'fr', 'FOSOAuthServerBundle');
        $instance->addResource('yml', ($this->targetDirs[3].'/vendor/friendsofsymfony/oauth-server-bundle/FOS/OAuthServerBundle/Resources/translations/FOSOAuthServerBundle.de.yml'), 'de', 'FOSOAuthServerBundle');
        $instance->addResource('yml', ($this->targetDirs[3].'/vendor/willdurand/oauth-server-bundle/Resources/translations/BazingaOAuthServerBundle.nl.yml'), 'nl', 'BazingaOAuthServerBundle');
        $instance->addResource('yml', ($this->targetDirs[3].'/vendor/willdurand/oauth-server-bundle/Resources/translations/BazingaOAuthServerBundle.de.yml'), 'de', 'BazingaOAuthServerBundle');
        $instance->addResource('yml', ($this->targetDirs[3].'/vendor/willdurand/oauth-server-bundle/Resources/translations/BazingaOAuthServerBundle.en.yml'), 'en', 'BazingaOAuthServerBundle');
        $instance->addResource('yml', ($this->targetDirs[3].'/vendor/willdurand/oauth-server-bundle/Resources/translations/BazingaOAuthServerBundle.fr.yml'), 'fr', 'BazingaOAuthServerBundle');
        $instance->addResource('yml', ($this->targetDirs[3].'/vendor/willdurand/oauth-server-bundle/Resources/translations/BazingaOAuthServerBundle.sl.yml'), 'sl', 'BazingaOAuthServerBundle');
        $instance->addResource('yml', ($this->targetDirs[3].'/vendor/oneup/uploader-bundle/Oneup/UploaderBundle/Resources/translations/OneupUploaderBundle.en.yml'), 'en', 'OneupUploaderBundle');
        return $instance;
    }
    protected function getUriSignerService()
    {
        return $this->services['uri_signer'] = new \Symfony\Component\HttpKernel\UriSigner('4c8d4301b58c8edcdd609e87134e4f8a63be002c3a3014a22f664d3b4e706f5a');
    }
    protected function getValidatorService()
    {
        return $this->services['validator'] = $this->get('validator.builder')->getValidator();
    }
    protected function getValidator_BuilderService()
    {
        $this->services['validator.builder'] = $instance = \Symfony\Component\Validator\Validation::createValidatorBuilder();
        $instance->setConstraintValidatorFactory(new \Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory($this, array('validator.expression' => 'validator.expression', 'Symfony\\Component\\Validator\\Constraints\\EmailValidator' => 'validator.email', 'security.validator.user_password' => 'security.validator.user_password', 'doctrine.orm.validator.unique' => 'doctrine.orm.validator.unique', 'leadlist_access' => 'mautic.validator.leadlistaccess', 'uniqueleadlist' => 'mautic.lead.constraint.alias', 'oauth_callback' => 'mautic.validator.oauthcallback')));
        $instance->setTranslator($this->get('translator.default'));
        $instance->setTranslationDomain('validators');
        $instance->addXmlMappings(array(0 => ($this->targetDirs[3].'/vendor/symfony/form/Symfony/Component/Form/Resources/config/validation.xml'), 1 => ($this->targetDirs[3].'/vendor/friendsofsymfony/oauth-server-bundle/FOS/OAuthServerBundle/Resources/config/validation.xml')));
        $instance->addMethodMapping('loadValidatorMetadata');
        $instance->setApiVersion(3);
        $instance->addObjectInitializers(array(0 => $this->get('doctrine.orm.validator_initializer')));
        return $instance;
    }
    protected function getValidator_EmailService()
    {
        return $this->services['validator.email'] = new \Symfony\Component\Validator\Constraints\EmailValidator(false);
    }
    protected function getValidator_ExpressionService()
    {
        return $this->services['validator.expression'] = new \Symfony\Component\Validator\Constraints\ExpressionValidator($this->get('property_accessor'));
    }
    protected function getBazinga_Oauth_EntityManagerService()
    {
        return $this->services['bazinga.oauth.entity_manager'] = $this->get('doctrine')->getManager(NULL);
    }
    protected function getBazinga_Oauth_Provider_TokenProviderService()
    {
        return $this->services['bazinga.oauth.provider.token_provider'] = new \Bazinga\OAuthServerBundle\Doctrine\Provider\TokenProvider($this->get('bazinga.oauth.entity_manager'), 'Mautic\\ApiBundle\\Entity\\oAuth1\\RequestToken', 'Mautic\\ApiBundle\\Entity\\oAuth1\\AccessToken');
    }
    protected function getControllerNameConverterService()
    {
        return $this->services['controller_name_converter'] = new \Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser($this->get('kernel'));
    }
    protected function getFosOauthServer_EntityManagerService()
    {
        return $this->services['fos_oauth_server.entity_manager'] = $this->get('doctrine')->getManager(NULL);
    }
    protected function getJmsSerializer_UnserializeObjectConstructorService()
    {
        return $this->services['jms_serializer.unserialize_object_constructor'] = new \JMS\Serializer\Construction\UnserializeObjectConstructor();
    }
    protected function getRouter_RequestContextService()
    {
        return $this->services['router.request_context'] = new \Symfony\Component\Routing\RequestContext('', 'GET', 'ma.labots.co', 'http', 80, 443);
    }
    protected function getSecurity_Access_DecisionManagerService()
    {
        return $this->services['security.access.decision_manager'] = new \Symfony\Component\Security\Core\Authorization\AccessDecisionManager(array(0 => new \Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter(new \Symfony\Component\Security\Core\Role\RoleHierarchy(array('ROLE_ADMIN' => array(0 => 'ROLE_USER')))), 1 => new \Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter($this->get('security.authentication.trust_resolver'))), 'affirmative', false, true);
    }
    protected function getSecurity_AccessListenerService()
    {
        return $this->services['security.access_listener'] = new \Symfony\Component\Security\Http\Firewall\AccessListener($this->get('security.context'), $this->get('security.access.decision_manager'), $this->get('security.access_map'), $this->get('security.authentication.manager'));
    }
    protected function getSecurity_AccessMapService()
    {
        $this->services['security.access_map'] = $instance = new \Symfony\Component\Security\Http\AccessMap();
        $instance->add(new \Symfony\Component\HttpFoundation\RequestMatcher('^/api'), array(0 => 'IS_AUTHENTICATED_FULLY'), NULL);
        return $instance;
    }
    protected function getSecurity_Authentication_ManagerService()
    {
        $a = $this->get('mautic.user.provider');
        $b = $this->get('security.encoder_factory');
        $c = new \Symfony\Component\Security\Core\User\UserChecker();
        $d = new \Mautic\ApiBundle\Security\OAuth1\Authentication\Provider\OAuthProvider($a, $this->get('bazinga.oauth.server_service'), '');
        $d->setFactory($this->get('mautic.factory'));
        $this->services['security.authentication.manager'] = $instance = new \Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager(array(0 => new \Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider('567aa2dbd72de8.36627330'), 1 => new \Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider('567aa2dbd72de8.36627330'), 2 => new \Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider($a, $c, 'oauth2_area', $b, true), 3 => new \Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider('567aa2dbd72de8.36627330'), 4 => new \Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider($a, $c, 'oauth1_area', $b, true), 5 => new \Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider('567aa2dbd72de8.36627330'), 6 => new \FOS\OAuthServerBundle\Security\Authentication\Provider\OAuthProvider($a, $this->get('fos_oauth_server.server'), $c), 7 => $d, 8 => new \Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider($a, $c, 'main', $b, true), 9 => new \Symfony\Component\Security\Core\Authentication\Provider\RememberMeAuthenticationProvider($c, 'ab90c625f80f8126309fec4fae1da7fff78001dd', 'main'), 10 => new \Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider('567aa2dbd72de8.36627330')), true);
        $instance->setEventDispatcher($this->get('event_dispatcher'));
        return $instance;
    }
    protected function getSecurity_Authentication_SessionStrategyService()
    {
        return $this->services['security.authentication.session_strategy'] = new \Symfony\Component\Security\Http\Session\SessionAuthenticationStrategy('migrate');
    }
    protected function getSecurity_Authentication_TrustResolverService()
    {
        return $this->services['security.authentication.trust_resolver'] = new \Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver('Symfony\\Component\\Security\\Core\\Authentication\\Token\\AnonymousToken', 'Symfony\\Component\\Security\\Core\\Authentication\\Token\\RememberMeToken');
    }
    protected function getSecurity_ChannelListenerService()
    {
        return $this->services['security.channel_listener'] = new \Symfony\Component\Security\Http\Firewall\ChannelListener($this->get('security.access_map'), new \Symfony\Component\Security\Http\EntryPoint\RetryAuthenticationEntryPoint(80, 443), $this->get('monolog.logger.security', ContainerInterface::NULL_ON_INVALID_REFERENCE));
    }
    protected function getSecurity_ContextListener_1Service()
    {
        return $this->services['security.context_listener.1'] = new \Symfony\Component\Security\Http\Firewall\ContextListener($this->get('security.context'), array(0 => $this->get('mautic.user.provider')), 'mautic', $this->get('monolog.logger.security', ContainerInterface::NULL_ON_INVALID_REFERENCE), $this->get('event_dispatcher', ContainerInterface::NULL_ON_INVALID_REFERENCE));
    }
    protected function getSecurity_HttpUtilsService()
    {
        $a = $this->get('router', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        return $this->services['security.http_utils'] = new \Symfony\Component\Security\Http\HttpUtils($a, $a);
    }
    protected function getSession_Storage_MetadataBagService()
    {
        return $this->services['session.storage.metadata_bag'] = new \Symfony\Component\HttpFoundation\Session\Storage\MetadataBag('_sf2_meta', '0');
    }
    protected function getTemplating_LocatorService()
    {
        return $this->services['templating.locator'] = new \Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator($this->get('file_locator'), __DIR__);
    }
    public function getParameter($name)
    {
        $name = strtolower($name);
        if (!(isset($this->parameters[$name]) || array_key_exists($name, $this->parameters))) {
            throw new InvalidArgumentException(sprintf('The parameter "%s" must be defined.', $name));
        }
        return $this->parameters[$name];
    }
    public function hasParameter($name)
    {
        $name = strtolower($name);
        return isset($this->parameters[$name]) || array_key_exists($name, $this->parameters);
    }
    public function setParameter($name, $value)
    {
        throw new LogicException('Impossible to call set() on a frozen ParameterBag.');
    }
    public function getParameterBag()
    {
        if (null === $this->parameterBag) {
            $this->parameterBag = new FrozenParameterBag($this->parameters);
        }
        return $this->parameterBag;
    }
    protected function getDefaultParameters()
    {
        return array(
            'kernel.root_dir' => $this->targetDirs[2],
            'kernel.environment' => 'prod',
            'kernel.debug' => false,
            'kernel.name' => 'app',
            'kernel.cache_dir' => __DIR__,
            'kernel.logs_dir' => ($this->targetDirs[2].'/logs'),
            'kernel.bundles' => array(
                'FrameworkBundle' => 'Symfony\\Bundle\\FrameworkBundle\\FrameworkBundle',
                'SecurityBundle' => 'Symfony\\Bundle\\SecurityBundle\\SecurityBundle',
                'MonologBundle' => 'Symfony\\Bundle\\MonologBundle\\MonologBundle',
                'SwiftmailerBundle' => 'Symfony\\Bundle\\SwiftmailerBundle\\SwiftmailerBundle',
                'DoctrineBundle' => 'Doctrine\\Bundle\\DoctrineBundle\\DoctrineBundle',
                'DoctrineCacheBundle' => 'Doctrine\\Bundle\\DoctrineCacheBundle\\DoctrineCacheBundle',
                'DoctrineFixturesBundle' => 'Doctrine\\Bundle\\FixturesBundle\\DoctrineFixturesBundle',
                'DoctrineMigrationsBundle' => 'Doctrine\\Bundle\\MigrationsBundle\\DoctrineMigrationsBundle',
                'KnpMenuBundle' => 'Knp\\Bundle\\MenuBundle\\KnpMenuBundle',
                'FOSOAuthServerBundle' => 'FOS\\OAuthServerBundle\\FOSOAuthServerBundle',
                'BazingaOAuthServerBundle' => 'Bazinga\\OAuthServerBundle\\BazingaOAuthServerBundle',
                'FOSRestBundle' => 'FOS\\RestBundle\\FOSRestBundle',
                'JMSSerializerBundle' => 'JMS\\SerializerBundle\\JMSSerializerBundle',
                'OneupUploaderBundle' => 'Oneup\\UploaderBundle\\OneupUploaderBundle',
                'MauticFormBundle' => 'Mautic\\FormBundle\\MauticFormBundle',
                'MauticCategoryBundle' => 'Mautic\\CategoryBundle\\MauticCategoryBundle',
                'MauticLeadBundle' => 'Mautic\\LeadBundle\\MauticLeadBundle',
                'MauticReportBundle' => 'Mautic\\ReportBundle\\MauticReportBundle',
                'MauticPageBundle' => 'Mautic\\PageBundle\\MauticPageBundle',
                'MauticCampaignBundle' => 'Mautic\\CampaignBundle\\MauticCampaignBundle',
                'MauticPointBundle' => 'Mautic\\PointBundle\\MauticPointBundle',
                'MauticApiBundle' => 'Mautic\\ApiBundle\\MauticApiBundle',
                'MauticUserBundle' => 'Mautic\\UserBundle\\MauticUserBundle',
                'MauticEmailBundle' => 'Mautic\\EmailBundle\\MauticEmailBundle',
                'MauticDashboardBundle' => 'Mautic\\DashboardBundle\\MauticDashboardBundle',
                'MauticPluginBundle' => 'Mautic\\PluginBundle\\MauticPluginBundle',
                'MauticInstallBundle' => 'Mautic\\InstallBundle\\MauticInstallBundle',
                'MauticConfigBundle' => 'Mautic\\ConfigBundle\\MauticConfigBundle',
                'MauticWebhookBundle' => 'Mautic\\WebhookBundle\\MauticWebhookBundle',
                'MauticCoreBundle' => 'Mautic\\CoreBundle\\MauticCoreBundle',
                'MauticCalendarBundle' => 'Mautic\\CalendarBundle\\MauticCalendarBundle',
                'MauticAssetBundle' => 'Mautic\\AssetBundle\\MauticAssetBundle',
                'MauticCrmBundle' => 'MauticPlugin\\MauticCrmBundle\\MauticCrmBundle',
                'MauticEmailMarketingBundle' => 'MauticPlugin\\MauticEmailMarketingBundle\\MauticEmailMarketingBundle',
                'MauticCloudStorageBundle' => 'MauticPlugin\\MauticCloudStorageBundle\\MauticCloudStorageBundle',
                'MauticSocialBundle' => 'MauticPlugin\\MauticSocialBundle\\MauticSocialBundle',
            ),
            'kernel.charset' => 'UTF-8',
            'kernel.container_class' => 'appProdProjectContainer',
            'mautic.bundles' => array(
                'MauticCoreBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Core',
                    'bundle' => 'CoreBundle',
                    'namespace' => 'Mautic\\CoreBundle',
                    'symfonyBundleName' => 'MauticCoreBundle',
                    'bundleClass' => 'Mautic\\CoreBundle\\MauticCoreBundle',
                    'relative' => 'app/bundles/CoreBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/CoreBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_core_ajax' => array(
                                    'path' => '/ajax',
                                    'controller' => 'MauticCoreBundle:Ajax:delegateAjax',
                                ),
                                'mautic_core_update' => array(
                                    'path' => '/update',
                                    'controller' => 'MauticCoreBundle:Update:index',
                                ),
                                'mautic_core_update_schema' => array(
                                    'path' => '/update/schema',
                                    'controller' => 'MauticCoreBundle:Update:schema',
                                ),
                                'mautic_core_form_action' => array(
                                    'path' => '/action/{objectAction}/{objectModel}/{objectId}',
                                    'controller' => 'MauticCoreBundle:Form:execute',
                                    'defaults' => array(
                                        'objectModel' => '',
                                    ),
                                ),
                            ),
                            'public' => array(
                                'mautic_base_index' => array(
                                    'path' => '/',
                                    'controller' => 'MauticCoreBundle:Default:index',
                                ),
                                'mautic_secure_root' => array(
                                    'path' => '/s',
                                    'controller' => 'MauticCoreBundle:Default:redirectSecureRoot',
                                ),
                                'mautic_secure_root_slash' => array(
                                    'path' => '/s/',
                                    'controller' => 'MauticCoreBundle:Default:redirectSecureRoot',
                                ),
                                'mautic_remove_trailing_slash' => array(
                                    'path' => '/{url}',
                                    'controller' => 'MauticCoreBundle:Common:removeTrailingSlash',
                                    'method' => 'GET',
                                    'requirements' => array(
                                        'url' => '.*/$',
                                    ),
                                ),
                                'mautic_public_bc_redirect' => array(
                                    'path' => '/p/{url}',
                                    'controller' => 'MauticCoreBundle:Default:publicBcRedirect',
                                    'requirements' => array(
                                        'url' => '.+',
                                    ),
                                ),
                                'mautic_ajax_bc_redirect' => array(
                                    'path' => '/ajax{url}',
                                    'controller' => 'MauticCoreBundle:Default:ajaxBcRedirect',
                                    'requirements' => array(
                                        'url' => '.+',
                                    ),
                                    'defaults' => array(
                                        'url' => '',
                                    ),
                                ),
                                'mautic_update_bc_redirect' => array(
                                    'path' => '/update',
                                    'controller' => 'MauticCoreBundle:Default:updateBcRedirect',
                                ),
                            ),
                        ),
                        'menu' => array(
                            'main' => array(
                                'priority' => -1000,
                                'items' => array(
                                    'name' => 'root',
                                    'children' => array(
                                    ),
                                ),
                            ),
                            'admin' => array(
                                'priority' => -1000,
                                'items' => array(
                                    'name' => 'admin',
                                    'children' => array(
                                    ),
                                ),
                            ),
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.core.subscriber' => array(
                                    'class' => 'Mautic\\CoreBundle\\EventListener\\CoreSubscriber',
                                ),
                                'mautic.core.auditlog.subscriber' => array(
                                    'class' => 'Mautic\\CoreBundle\\EventListener\\AuditLogSubscriber',
                                ),
                                'mautic.core.configbundle.subscriber' => array(
                                    'class' => 'Mautic\\CoreBundle\\EventListener\\ConfigSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.form.type.spacer' => array(
                                    'class' => 'Mautic\\CoreBundle\\Form\\Type\\SpacerType',
                                    'alias' => 'spacer',
                                ),
                                'mautic.form.type.tel' => array(
                                    'class' => 'Mautic\\CoreBundle\\Form\\Type\\TelType',
                                    'alias' => 'tel',
                                ),
                                'mautic.form.type.button_group' => array(
                                    'class' => 'Mautic\\CoreBundle\\Form\\Type\\ButtonGroupType',
                                    'alias' => 'button_group',
                                ),
                                'mautic.form.type.yesno_button_group' => array(
                                    'class' => 'Mautic\\CoreBundle\\Form\\Type\\YesNoButtonGroupType',
                                    'alias' => 'yesno_button_group',
                                ),
                                'mautic.form.type.standalone_button' => array(
                                    'class' => 'Mautic\\CoreBundle\\Form\\Type\\StandAloneButtonType',
                                    'alias' => 'standalone_button',
                                ),
                                'mautic.form.type.form_buttons' => array(
                                    'class' => 'Mautic\\CoreBundle\\Form\\Type\\FormButtonsType',
                                    'alias' => 'form_buttons',
                                ),
                                'mautic.form.type.hidden_entity' => array(
                                    'class' => 'Mautic\\CoreBundle\\Form\\Type\\HiddenEntityType',
                                    'alias' => 'hidden_entity',
                                    'arguments' => 'doctrine.orm.entity_manager',
                                ),
                                'mautic.form.type.sortablelist' => array(
                                    'class' => 'Mautic\\CoreBundle\\Form\\Type\\SortableListType',
                                    'alias' => 'sortablelist',
                                ),
                                'mautic.form.type.dynamiclist' => array(
                                    'class' => 'Mautic\\CoreBundle\\Form\\Type\\DynamicListType',
                                    'alias' => 'dynamiclist',
                                ),
                                'mautic.form.type.coreconfig' => array(
                                    'class' => 'Mautic\\CoreBundle\\Form\\Type\\ConfigType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'coreconfig',
                                ),
                                'mautic.form.type.theme_list' => array(
                                    'class' => 'Mautic\\CoreBundle\\Form\\Type\\ThemeListType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'theme_list',
                                ),
                            ),
                            'helpers' => array(
                                'mautic.helper.menu' => array(
                                    'class' => 'Mautic\\CoreBundle\\Menu\\MenuHelper',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'menu_helper',
                                ),
                                'mautic.helper.template.date' => array(
                                    'class' => 'Mautic\\CoreBundle\\Templating\\Helper\\DateHelper',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'date',
                                ),
                                'mautic.helper.template.exception' => array(
                                    'class' => 'Mautic\\CoreBundle\\Templating\\Helper\\ExceptionHelper',
                                    'arguments' => $this->targetDirs[2],
                                    'alias' => 'exception',
                                ),
                                'mautic.helper.template.gravatar' => array(
                                    'class' => 'Mautic\\CoreBundle\\Templating\\Helper\\GravatarHelper',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'gravatar',
                                ),
                                'mautic.helper.template.analytics' => array(
                                    'class' => 'Mautic\\CoreBundle\\Templating\\Helper\\AnalyticsHelper',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'analytics',
                                ),
                                'mautic.helper.template.mautibot' => array(
                                    'class' => 'Mautic\\CoreBundle\\Templating\\Helper\\MautibotHelper',
                                    'alias' => 'mautibot',
                                ),
                                'mautic.helper.template.canvas' => array(
                                    'class' => 'Mautic\\CoreBundle\\Templating\\Helper\\SidebarCanvasHelper',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'canvas',
                                ),
                                'mautic.helper.template.button' => array(
                                    'class' => 'Mautic\\CoreBundle\\Templating\\Helper\\ButtonHelper',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'buttons',
                                ),
                                'mautic.helper.template.formatter' => array(
                                    'class' => 'Mautic\\CoreBundle\\Templating\\Helper\\FormatterHelper',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'formatter',
                                ),
                                'mautic.helper.template.security' => array(
                                    'class' => 'Mautic\\CoreBundle\\Templating\\Helper\\SecurityHelper',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'security',
                                ),
                            ),
                            'other' => array(
                                'mautic.core.errorhandler.subscriber' => array(
                                    'class' => 'Mautic\\CoreBundle\\EventListener\\ErrorHandlingListener',
                                    'arguments' => array(
                                        0 => 'prod',
                                        1 => 'monolog.logger.mautic',
                                    ),
                                    'tag' => 'kernel.event_subscriber',
                                ),
                                'templating.helper.assets.class' => 'Mautic\\CoreBundle\\Templating\\Helper\\AssetsHelper',
                                'templating.helper.slots.class' => 'Mautic\\CoreBundle\\Templating\\Helper\\SlotsHelper',
                                'templating.name_parser.class' => 'Mautic\\CoreBundle\\Templating\\TemplateNameParser',
                                'templating.helper.form.class' => 'Mautic\\CoreBundle\\Templating\\Helper\\FormHelper',
                                'translator.class' => 'Mautic\\CoreBundle\\Translation\\Translator',
                                'templating.helper.translator.class' => 'Mautic\\CoreBundle\\Templating\\Helper\\TranslatorHelper',
                                'mautic.factory' => array(
                                    'class' => 'Mautic\\CoreBundle\\Factory\\MauticFactory',
                                    'arguments' => 'service_container',
                                ),
                                'mautic.templating.name_parser' => array(
                                    'class' => 'Mautic\\CoreBundle\\Templating\\TemplateNameParser',
                                    'arguments' => 'kernel',
                                ),
                                'mautic.route_loader' => array(
                                    'class' => 'Mautic\\CoreBundle\\Loader\\RouteLoader',
                                    'arguments' => 'mautic.factory',
                                    'tag' => 'routing.loader',
                                ),
                                'mautic.security' => array(
                                    'class' => 'Mautic\\CoreBundle\\Security\\Permissions\\CorePermissions',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.translation.loader' => array(
                                    'class' => 'Mautic\\CoreBundle\\Loader\\TranslationLoader',
                                    'arguments' => 'mautic.factory',
                                    'tag' => 'translation.loader',
                                    'alias' => 'mautic',
                                ),
                                'mautic.tblprefix_subscriber' => array(
                                    'class' => 'Mautic\\CoreBundle\\EventListener\\DoctrineEventsSubscriber',
                                    'tag' => 'doctrine.event_subscriber',
                                ),
                                'mautic.exception.listener' => array(
                                    'class' => 'Mautic\\CoreBundle\\EventListener\\ExceptionListener',
                                    'arguments' => array(
                                        0 => '"MauticCoreBundle:Exception:show"',
                                        1 => 'monolog.logger.mautic',
                                    ),
                                    'tag' => 'kernel.event_listener',
                                    'tagArguments' => array(
                                        'event' => 'kernel.exception',
                                        'method' => 'onKernelException',
                                        'priority' => 255,
                                    ),
                                ),
                                'transifex' => array(
                                    'class' => 'BabDev\\Transifex\\Transifex',
                                    'arguments' => array(
                                        0 => array(
                                            'api.username' => '',
                                            'api.password' => '',
                                        ),
                                    ),
                                ),
                                'mautic.helper.assetgeneration' => array(
                                    'class' => 'Mautic\\CoreBundle\\Helper\\AssetGenerationHelper',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.helper.cookie' => array(
                                    'class' => 'Mautic\\CoreBundle\\Helper\\CookieHelper',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.helper.update' => array(
                                    'class' => 'Mautic\\CoreBundle\\Helper\\UpdateHelper',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.helper.cache' => array(
                                    'class' => 'Mautic\\CoreBundle\\Helper\\CacheHelper',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.helper.theme' => array(
                                    'class' => 'Mautic\\CoreBundle\\Helper\\ThemeHelper',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.helper.encryption' => array(
                                    'class' => 'Mautic\\CoreBundle\\Helper\\EncryptionHelper',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.helper.language' => array(
                                    'class' => 'Mautic\\CoreBundle\\Helper\\LanguageHelper',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.menu_renderer' => array(
                                    'class' => 'Mautic\\CoreBundle\\Menu\\MenuRenderer',
                                    'arguments' => array(
                                        0 => 'knp_menu.matcher',
                                        1 => 'mautic.factory',
                                        2 => 'UTF-8',
                                    ),
                                    'tag' => 'knp_menu.renderer',
                                    'alias' => 'mautic',
                                ),
                                'mautic.menu.builder' => array(
                                    'class' => 'Mautic\\CoreBundle\\Menu\\MenuBuilder',
                                    'arguments' => array(
                                        0 => 'knp_menu.factory',
                                        1 => 'knp_menu.matcher',
                                        2 => 'mautic.factory',
                                    ),
                                ),
                                'mautic.menu.main' => array(
                                    'class' => 'Knp\\Menu\\MenuItem',
                                    'factoryService' => 'mautic.menu.builder',
                                    'factoryMethod' => 'mainMenu',
                                    'tag' => 'knp_menu.menu',
                                    'alias' => 'main',
                                ),
                                'mautic.menu.admin' => array(
                                    'class' => 'Knp\\Menu\\MenuItem',
                                    'factoryService' => 'mautic.menu.builder',
                                    'factoryMethod' => 'adminMenu',
                                    'tag' => 'knp_menu.menu',
                                    'alias' => 'admin',
                                ),
                                'twig.controller.exception.class' => 'Mautic\\CoreBundle\\Controller\\ExceptionController',
                                'monolog.handler.stream.class' => 'Mautic\\CoreBundle\\Monolog\\Handler\\PhpHandler',
                            ),
                        ),
                        'ip_lookup_services' => array(
                            'freegeoip' => array(
                                'display_name' => 'freegeoip.net',
                                'class' => 'Mautic\\CoreBundle\\IpLookup\\FreegeoipIpLookup',
                            ),
                            'geobytes' => array(
                                'display_name' => 'Geobytes',
                                'class' => 'Mautic\\CoreBundle\\IpLookup\\GeobytesIpLookup',
                            ),
                            'geoips' => array(
                                'display_name' => 'GeoIPs',
                                'class' => 'Mautic\\CoreBundle\\IpLookup\\GeoipsIpLookup',
                            ),
                            'ipinfodb' => array(
                                'display_name' => 'IPInfoDB',
                                'class' => 'Mautic\\CoreBundle\\IpLookup\\IpinfodbIpLookup',
                            ),
                            'maxmind_country' => array(
                                'display_name' => 'MaxMind - Country Geolocation',
                                'class' => 'Mautic\\CoreBundle\\IpLookup\\MaxmindCountryIpLookup',
                            ),
                            'maxmind_omni' => array(
                                'display_name' => 'MaxMind - Insights (formerly Omni)',
                                'class' => 'Mautic\\CoreBundle\\IpLookup\\MaxmindOmniIpLookup',
                            ),
                            'maxmind_precision' => array(
                                'display_name' => 'MaxMind - GeoIP2 Precision',
                                'class' => 'Mautic\\CoreBundle\\IpLookup\\MaxmindPrecisionIpLookup',
                            ),
                            'telize' => array(
                                'display_name' => 'Telize',
                                'class' => 'Mautic\\CoreBundle\\IpLookup\\TelizeIpLookup',
                            ),
                        ),
                        'parameters' => array(
                            'site_url' => '',
                            'webroot' => '',
                            'cache_path' => $this->targetDirs[1],
                            'log_path' => ($this->targetDirs[2].'/logs'),
                            'image_path' => 'media/images',
                            'theme' => 'Mauve',
                            'db_driver' => 'pdo_mysql',
                            'db_host' => 'localhost',
                            'db_port' => 3306,
                            'db_name' => '',
                            'db_user' => '',
                            'db_password' => '',
                            'db_table_prefix' => '',
                            'db_path' => '',
                            'locale' => 'en_US',
                            'secret_key' => '',
                            'trusted_hosts' => NULL,
                            'trusted_proxies' => NULL,
                            'rememberme_key' => '6b3cb849046d271a79c0828c99719d7540d90073',
                            'rememberme_lifetime' => 31536000,
                            'rememberme_path' => '/',
                            'rememberme_domain' => '',
                            'default_pagelimit' => 30,
                            'default_timezone' => 'UTC',
                            'date_format_full' => 'F j, Y g:i a T',
                            'date_format_short' => 'D, M d',
                            'date_format_dateonly' => 'F j, Y',
                            'date_format_timeonly' => 'g:i a',
                            'ip_lookup_service' => 'telize',
                            'ip_lookup_auth' => '',
                            'transifex_username' => '',
                            'transifex_password' => '',
                            'update_stability' => 'stable',
                            'cookie_path' => '/',
                            'cookie_domain' => '',
                            'cookie_secure' => NULL,
                            'cookie_httponly' => false,
                            'do_not_track_ips' => NULL,
                        ),
                    ),
                ),
                'MauticFormBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Form',
                    'bundle' => 'FormBundle',
                    'namespace' => 'Mautic\\FormBundle',
                    'symfonyBundleName' => 'MauticFormBundle',
                    'bundleClass' => 'Mautic\\FormBundle\\MauticFormBundle',
                    'relative' => 'app/bundles/FormBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/FormBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_form_pagetoken_index' => array(
                                    'path' => '/forms/pagetokens/{page}',
                                    'controller' => 'MauticFormBundle:SubscribedEvents\\BuilderToken:index',
                                ),
                                'mautic_formaction_action' => array(
                                    'path' => '/forms/action/{objectAction}/{objectId}',
                                    'controller' => 'MauticFormBundle:Action:execute',
                                ),
                                'mautic_formfield_action' => array(
                                    'path' => '/forms/field/{objectAction}/{objectId}',
                                    'controller' => 'MauticFormBundle:Field:execute',
                                ),
                                'mautic_form_index' => array(
                                    'path' => '/forms/{page}',
                                    'controller' => 'MauticFormBundle:Form:index',
                                ),
                                'mautic_form_results' => array(
                                    'path' => '/forms/results/{objectId}/{page}',
                                    'controller' => 'MauticFormBundle:Result:index',
                                ),
                                'mautic_form_export' => array(
                                    'path' => '/forms/results/{objectId}/export/{format}',
                                    'controller' => 'MauticFormBundle:Result:export',
                                    'defaults' => array(
                                        'format' => 'csv',
                                    ),
                                ),
                                'mautic_form_results_delete' => array(
                                    'path' => '/forms/results/{formId}/delete/{objectId}',
                                    'controller' => 'MauticFormBundle:Result:delete',
                                    'defaults' => array(
                                        'objectId' => 0,
                                    ),
                                ),
                                'mautic_form_action' => array(
                                    'path' => '/forms/{objectAction}/{objectId}',
                                    'controller' => 'MauticFormBundle:Form:execute',
                                ),
                            ),
                            'api' => array(
                                'mautic_api_getforms' => array(
                                    'path' => '/forms',
                                    'controller' => 'MauticFormBundle:Api\\FormApi:getEntities',
                                ),
                                'mautic_api_getform' => array(
                                    'path' => '/forms/{id}',
                                    'controller' => 'MauticFormBundle:Api\\FormApi:getEntity',
                                ),
                            ),
                            'public' => array(
                                'mautic_form_postresults' => array(
                                    'path' => '/form/submit',
                                    'controller' => 'MauticFormBundle:Public:submit',
                                ),
                                'mautic_form_generateform' => array(
                                    'path' => '/form/generate.js',
                                    'controller' => 'MauticFormBundle:Public:generate',
                                ),
                                'mautic_form_postmessage' => array(
                                    'path' => '/form/message',
                                    'controller' => 'MauticFormBundle:Public:message',
                                ),
                                'mautic_form_preview' => array(
                                    'path' => '/form/{id}',
                                    'controller' => 'MauticFormBundle:Public:preview',
                                    'defaults' => array(
                                        'id' => '0',
                                    ),
                                ),
                            ),
                        ),
                        'menu' => array(
                            'main' => array(
                                'priority' => 20,
                                'items' => array(
                                    'mautic.form.forms' => array(
                                        'route' => 'mautic_form_index',
                                        'id' => 'mautic_form_root',
                                        'iconClass' => 'fa-pencil-square-o',
                                        'access' => array(
                                            0 => 'form:forms:viewown',
                                            1 => 'form:forms:viewother',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        'categories' => array(
                            'form' => NULL,
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.form.subscriber' => array(
                                    'class' => 'Mautic\\FormBundle\\EventListener\\FormSubscriber',
                                ),
                                'mautic.form.pagebundle.subscriber' => array(
                                    'class' => 'Mautic\\FormBundle\\EventListener\\PageSubscriber',
                                ),
                                'mautic.form.pointbundle.subscriber' => array(
                                    'class' => 'Mautic\\FormBundle\\EventListener\\PointSubscriber',
                                ),
                                'mautic.form.reportbundle.subscriber' => array(
                                    'class' => 'Mautic\\FormBundle\\EventListener\\ReportSubscriber',
                                ),
                                'mautic.form.campaignbundle.subscriber' => array(
                                    'class' => 'Mautic\\FormBundle\\EventListener\\CampaignSubscriber',
                                ),
                                'mautic.form.calendarbundle.subscriber' => array(
                                    'class' => 'Mautic\\FormBundle\\EventListener\\CalendarSubscriber',
                                ),
                                'mautic.form.leadbundle.subscriber' => array(
                                    'class' => 'Mautic\\FormBundle\\EventListener\\LeadSubscriber',
                                ),
                                'mautic.form.emailbundle.subscriber' => array(
                                    'class' => 'Mautic\\FormBundle\\EventListener\\EmailSubscriber',
                                ),
                                'mautic.form.search.subscriber' => array(
                                    'class' => 'Mautic\\FormBundle\\EventListener\\SearchSubscriber',
                                ),
                                'mautic.form.webhook.subscriber' => array(
                                    'class' => 'Mautic\\FormBundle\\EventListener\\WebhookSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.form.type.form' => array(
                                    'class' => 'Mautic\\FormBundle\\Form\\Type\\FormType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'mauticform',
                                ),
                                'mautic.form.type.field' => array(
                                    'class' => 'Mautic\\FormBundle\\Form\\Type\\FieldType',
                                    'alias' => 'formfield',
                                ),
                                'mautic.form.type.action' => array(
                                    'class' => 'Mautic\\FormBundle\\Form\\Type\\ActionType',
                                    'alias' => 'formaction',
                                ),
                                'mautic.form.type.field_propertytext' => array(
                                    'class' => 'Mautic\\FormBundle\\Form\\Type\\FormFieldTextType',
                                    'alias' => 'formfield_text',
                                ),
                                'mautic.form.type.field_propertyplaceholder' => array(
                                    'class' => 'Mautic\\FormBundle\\Form\\Type\\FormFieldPlaceholderType',
                                    'alias' => 'formfield_placeholder',
                                ),
                                'mautic.form.type.field_propertyselect' => array(
                                    'class' => 'Mautic\\FormBundle\\Form\\Type\\FormFieldSelectType',
                                    'alias' => 'formfield_select',
                                ),
                                'mautic.form.type.field_propertycaptcha' => array(
                                    'class' => 'Mautic\\FormBundle\\Form\\Type\\FormFieldCaptchaType',
                                    'alias' => 'formfield_captcha',
                                ),
                                'mautic.form.type.field_propertygroup' => array(
                                    'class' => 'Mautic\\FormBundle\\Form\\Type\\FormFieldGroupType',
                                    'alias' => 'formfield_group',
                                ),
                                'mautic.form.type.pointaction_formsubmit' => array(
                                    'class' => 'Mautic\\FormBundle\\Form\\Type\\PointActionFormSubmitType',
                                    'alias' => 'pointaction_formsubmit',
                                ),
                                'mautic.form.type.form_list' => array(
                                    'class' => 'Mautic\\FormBundle\\Form\\Type\\FormListType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'form_list',
                                ),
                                'mautic.form.type.campaignevent_formsubmit' => array(
                                    'class' => 'Mautic\\FormBundle\\Form\\Type\\CampaignEventFormSubmitType',
                                    'alias' => 'campaignevent_formsubmit',
                                ),
                                'mautic.form.type.campaignevent_form_field_value' => array(
                                    'class' => 'Mautic\\FormBundle\\Form\\Type\\CampaignEventFormFieldValueType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'campaignevent_form_field_value',
                                ),
                                'mautic.form.type.form_submitaction_sendemail' => array(
                                    'class' => 'Mautic\\FormBundle\\Form\\Type\\SubmitActionEmailType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'form_submitaction_sendemail',
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticCategoryBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Category',
                    'bundle' => 'CategoryBundle',
                    'namespace' => 'Mautic\\CategoryBundle',
                    'symfonyBundleName' => 'MauticCategoryBundle',
                    'bundleClass' => 'Mautic\\CategoryBundle\\MauticCategoryBundle',
                    'relative' => 'app/bundles/CategoryBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/CategoryBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_category_index' => array(
                                    'path' => '/categories/{bundle}/{page}',
                                    'controller' => 'MauticCategoryBundle:Category:index',
                                    'defaults' => array(
                                        'bundle' => 'category',
                                    ),
                                ),
                                'mautic_category_action' => array(
                                    'path' => '/categories/{bundle}/{objectAction}/{objectId}',
                                    'controller' => 'MauticCategoryBundle:Category:executeCategory',
                                    'defaults' => array(
                                        'bundle' => 'category',
                                    ),
                                ),
                            ),
                        ),
                        'menu' => array(
                            'admin' => array(
                                'mautic.category.menu.index' => array(
                                    'route' => 'mautic_category_index',
                                    'iconClass' => 'fa-folder',
                                    'id' => 'mautic_category_index',
                                ),
                            ),
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.category.subscriber' => array(
                                    'class' => 'Mautic\\CategoryBundle\\EventListener\\CategorySubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.form.type.category' => array(
                                    'class' => 'Mautic\\CategoryBundle\\Form\\Type\\CategoryListType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'category',
                                ),
                                'mautic.form.type.category_form' => array(
                                    'class' => 'Mautic\\CategoryBundle\\Form\\Type\\CategoryType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'category_form',
                                ),
                                'mautic.form.type.category_bundles_form' => array(
                                    'class' => 'Mautic\\CategoryBundle\\Form\\Type\\CategoryBundlesType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'category_bundles_form',
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticLeadBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Lead',
                    'bundle' => 'LeadBundle',
                    'namespace' => 'Mautic\\LeadBundle',
                    'symfonyBundleName' => 'MauticLeadBundle',
                    'bundleClass' => 'Mautic\\LeadBundle\\MauticLeadBundle',
                    'relative' => 'app/bundles/LeadBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/LeadBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_lead_emailtoken_index' => array(
                                    'path' => '/leads/emailtokens/{page}',
                                    'controller' => 'MauticLeadBundle:SubscribedEvents\\BuilderToken:index',
                                ),
                                'mautic_leadlist_index' => array(
                                    'path' => '/leads/lists/{page}',
                                    'controller' => 'MauticLeadBundle:List:index',
                                ),
                                'mautic_leadlist_action' => array(
                                    'path' => '/leads/lists/{objectAction}/{objectId}',
                                    'controller' => 'MauticLeadBundle:List:execute',
                                ),
                                'mautic_leadfield_index' => array(
                                    'path' => '/leads/fields/{page}',
                                    'controller' => 'MauticLeadBundle:Field:index',
                                ),
                                'mautic_leadfield_action' => array(
                                    'path' => '/leads/fields/{objectAction}/{objectId}',
                                    'controller' => 'MauticLeadBundle:Field:execute',
                                ),
                                'mautic_lead_index' => array(
                                    'path' => '/leads/{page}',
                                    'controller' => 'MauticLeadBundle:Lead:index',
                                ),
                                'mautic_leadnote_index' => array(
                                    'path' => '/leads/notes/{leadId}/{page}',
                                    'controller' => 'MauticLeadBundle:Note:index',
                                    'defaults' => array(
                                        'leadId' => 0,
                                    ),
                                    'requirements' => array(
                                        'leadId' => '\\d+',
                                    ),
                                ),
                                'mautic_leadnote_action' => array(
                                    'path' => '/leads/notes/{leadId}/{objectAction}/{objectId}',
                                    'controller' => 'MauticLeadBundle:Note:executeNote',
                                    'requirements' => array(
                                        'leadId' => '\\d+',
                                    ),
                                ),
                                'mautic_lead_action' => array(
                                    'path' => '/leads/{objectAction}/{objectId}',
                                    'controller' => 'MauticLeadBundle:Lead:execute',
                                ),
                            ),
                            'api' => array(
                                'mautic_api_getleads' => array(
                                    'path' => '/leads',
                                    'controller' => 'MauticLeadBundle:Api\\LeadApi:getEntities',
                                ),
                                'mautic_api_newlead' => array(
                                    'path' => '/leads/new',
                                    'controller' => 'MauticLeadBundle:Api\\LeadApi:newEntity',
                                    'method' => 'POST',
                                ),
                                'mautic_api_getlead' => array(
                                    'path' => '/leads/{id}',
                                    'controller' => 'MauticLeadBundle:Api\\LeadApi:getEntity',
                                ),
                                'mautic_api_editputlead' => array(
                                    'path' => '/leads/{id}/edit',
                                    'controller' => 'MauticLeadBundle:Api\\LeadApi:editEntity',
                                    'method' => 'PUT',
                                ),
                                'mautic_api_editpatchlead' => array(
                                    'path' => '/leads/{id}/edit',
                                    'controller' => 'MauticLeadBundle:Api\\LeadApi:editEntity',
                                    'method' => 'PATCH',
                                ),
                                'mautic_api_deletelead' => array(
                                    'path' => '/leads/{id}/delete',
                                    'controller' => 'MauticLeadBundle:Api\\LeadApi:deleteEntity',
                                    'method' => 'DELETE',
                                ),
                                'mautic_api_getleadsnotes' => array(
                                    'path' => '/leads/{id}/notes',
                                    'controller' => 'MauticLeadBundle:Api\\LeadApi:getNotes',
                                ),
                                'mautic_api_getleadscampaigns' => array(
                                    'path' => '/leads/{id}/campaigns',
                                    'controller' => 'MauticLeadBundle:Api\\LeadApi:getCampaigns',
                                ),
                                'mautic_api_getleadslists' => array(
                                    'path' => '/leads/{id}/lists',
                                    'controller' => 'MauticLeadBundle:Api\\LeadApi:getLists',
                                ),
                                'mautic_api_getleadowners' => array(
                                    'path' => '/leads/list/owners',
                                    'controller' => 'MauticLeadBundle:Api\\LeadApi:getOwners',
                                ),
                                'mautic_api_getleadfields' => array(
                                    'path' => '/leads/list/fields',
                                    'controller' => 'MauticLeadBundle:Api\\LeadApi:getFields',
                                ),
                                'mautic_api_getleadlists' => array(
                                    'path' => '/leads/list/lists',
                                    'controller' => 'MauticLeadBundle:Api\\ListApi:getLists',
                                ),
                                'mautic_api_getlists' => array(
                                    'path' => '/lists',
                                    'controller' => 'MauticLeadBundle:Api\\ListApi:getLists',
                                ),
                                'mautic_api_listaddlead' => array(
                                    'path' => '/lists/{id}/lead/add/{leadId}',
                                    'controller' => 'MauticLeadBundle:Api\\ListApi:addLead',
                                    'method' => 'POST',
                                ),
                                'mautic_api_listremovelead' => array(
                                    'path' => '/lists/{id}/lead/remove/{leadId}',
                                    'controller' => 'MauticLeadBundle:Api\\ListApi:removeLead',
                                    'method' => 'POST',
                                ),
                            ),
                        ),
                        'menu' => array(
                            'main' => array(
                                'priority' => 5,
                                'items' => array(
                                    'mautic.lead.leads' => array(
                                        'id' => 'menu_lead_parent',
                                        'iconClass' => 'fa-user',
                                        'access' => array(
                                            0 => 'lead:leads:viewown',
                                            1 => 'lead:leads:viewother',
                                        ),
                                        'children' => array(
                                            'mautic.lead.lead.menu.index' => array(
                                                'route' => 'mautic_lead_index',
                                            ),
                                            'mautic.lead.list.menu.index' => array(
                                                'route' => 'mautic_leadlist_index',
                                            ),
                                            'mautic.lead.field.menu.index' => array(
                                                'route' => 'mautic_leadfield_index',
                                                'access' => 'lead:fields:full',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.lead.subscriber' => array(
                                    'class' => 'Mautic\\LeadBundle\\EventListener\\LeadSubscriber',
                                ),
                                'mautic.lead.emailbundle.subscriber' => array(
                                    'class' => 'Mautic\\LeadBundle\\EventListener\\EmailSubscriber',
                                ),
                                'mautic.lead.formbundle.subscriber' => array(
                                    'class' => 'Mautic\\LeadBundle\\EventListener\\FormSubscriber',
                                ),
                                'mautic.lead.campaignbundle.subscriber' => array(
                                    'class' => 'Mautic\\LeadBundle\\EventListener\\CampaignSubscriber',
                                ),
                                'mautic.lead.reportbundle.subscriber' => array(
                                    'class' => 'Mautic\\LeadBundle\\EventListener\\ReportSubscriber',
                                ),
                                'mautic.lead.doctrine.subscriber' => array(
                                    'class' => 'Mautic\\LeadBundle\\EventListener\\DoctrineSubscriber',
                                    'tag' => 'doctrine.event_subscriber',
                                ),
                                'mautic.lead.calendarbundle.subscriber' => array(
                                    'class' => 'Mautic\\LeadBundle\\EventListener\\CalendarSubscriber',
                                ),
                                'mautic.lead.pointbundle.subscriber' => array(
                                    'class' => 'Mautic\\LeadBundle\\EventListener\\PointSubscriber',
                                ),
                                'mautic.lead.search.subscriber' => array(
                                    'class' => 'Mautic\\LeadBundle\\EventListener\\SearchSubscriber',
                                ),
                                'mautic.webhook.subscriber' => array(
                                    'class' => 'Mautic\\LeadBundle\\EventListener\\WebhookSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.form.type.lead' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\LeadType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'lead',
                                ),
                                'mautic.form.type.leadlist' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\ListType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'leadlist',
                                ),
                                'mautic.form.type.leadlist_choices' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\LeadListType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'leadlist_choices',
                                ),
                                'mautic.form.type.leadlist_filter' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\FilterType',
                                    'alias' => 'leadlist_filter',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.form.type.leadfield' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\FieldType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'leadfield',
                                ),
                                'mautic.form.type.lead.submitaction.pointschange' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\FormSubmitActionPointsChangeType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'lead_submitaction_pointschange',
                                ),
                                'mautic.form.type.lead.submitaction.changelist' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\EventListType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'leadlist_action_type',
                                ),
                                'mautic.form.type.leadpoints_trigger' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\PointTriggerType',
                                    'alias' => 'leadpoints_trigger',
                                ),
                                'mautic.form.type.leadpoints_action' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\PointActionType',
                                    'alias' => 'leadpoints_action',
                                ),
                                'mautic.form.type.leadlist_trigger' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\ListTriggerType',
                                    'alias' => 'leadlist_trigger',
                                ),
                                'mautic.form.type.leadlist_action' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\ListActionType',
                                    'alias' => 'leadlist_action',
                                ),
                                'mautic.form.type.updatelead_action' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\UpdateLeadActionType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'updatelead_action',
                                ),
                                'mautic.form.type.leadnote' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\NoteType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'leadnote',
                                ),
                                'mautic.form.type.lead_import' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\LeadImportType',
                                    'alias' => 'lead_import',
                                ),
                                'mautic.form.type.lead_field_import' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\LeadImportFieldType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'lead_field_import',
                                ),
                                'mautic.form.type.lead_quickemail' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\EmailType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'lead_quickemail',
                                ),
                                'mautic.form.type.lead_tags' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\TagListType',
                                    'alias' => 'lead_tags',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.form.type.lead_tag' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\TagType',
                                    'alias' => 'lead_tag',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.form.type.modify_lead_tags' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\ModifyLeadTagsType',
                                    'alias' => 'modify_lead_tags',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.form.type.lead_batch' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\BatchType',
                                    'alias' => 'lead_batch',
                                ),
                                'mautic.form.type.lead_batch_dnc' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\DncType',
                                    'alias' => 'lead_batch_dnc',
                                ),
                                'mautic.form.type.lead_merge' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\MergeType',
                                    'alias' => 'lead_merge',
                                ),
                                'mautic.form.type.campaignevent_lead_field_value' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\CampaignEventLeadFieldValueType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'campaignevent_lead_field_value',
                                ),
                                'mautic.form.type.lead_fields' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Type\\LeadFieldsType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'leadfields_choices',
                                ),
                            ),
                            'other' => array(
                                'mautic.validator.leadlistaccess' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Validator\\Constraints\\LeadListAccessValidator',
                                    'arguments' => 'mautic.factory',
                                    'tag' => 'validator.constraint_validator',
                                    'alias' => 'leadlist_access',
                                ),
                                'mautic.lead.constraint.alias' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Validator\\Constraints\\UniqueUserAliasValidator',
                                    'arguments' => 'mautic.factory',
                                    'tag' => 'validator.constraint_validator',
                                    'alias' => 'uniqueleadlist',
                                ),
                            ),
                            'helpers' => array(
                                'mautic.helper.template.avatar' => array(
                                    'class' => 'Mautic\\LeadBundle\\Templating\\Helper\\AvatarHelper',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'lead_avatar',
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticReportBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Report',
                    'bundle' => 'ReportBundle',
                    'namespace' => 'Mautic\\ReportBundle',
                    'symfonyBundleName' => 'MauticReportBundle',
                    'bundleClass' => 'Mautic\\ReportBundle\\MauticReportBundle',
                    'relative' => 'app/bundles/ReportBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/ReportBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_report_index' => array(
                                    'path' => '/reports/{page}',
                                    'controller' => 'MauticReportBundle:Report:index',
                                ),
                                'mautic_report_export' => array(
                                    'path' => '/reports/view/{objectId}/export/{format}',
                                    'controller' => 'MauticReportBundle:Report:export',
                                    'defaults' => array(
                                        'format' => 'csv',
                                    ),
                                ),
                                'mautic_report_view' => array(
                                    'path' => '/reports/view/{objectId}/{reportPage}',
                                    'controller' => 'MauticReportBundle:Report:view',
                                    'defaults' => array(
                                        'reportPage' => 1,
                                    ),
                                    'requirements' => array(
                                        'reportPage' => '\\d+',
                                    ),
                                ),
                                'mautic_report_action' => array(
                                    'path' => '/reports/{objectAction}/{objectId}',
                                    'controller' => 'MauticReportBundle:Report:execute',
                                ),
                            ),
                            'api' => array(
                                'mautic_api_getreports' => array(
                                    'path' => '/reports',
                                    'controller' => 'MauticReportBundle:Api\\ReportApi:getEntities',
                                ),
                                'mautic_api_getreport' => array(
                                    'path' => '/reports/{id}',
                                    'controller' => 'MauticReportBundle:Api\\ReportApi:getReport',
                                ),
                            ),
                        ),
                        'menu' => array(
                            'main' => array(
                                'priority' => 40,
                                'items' => array(
                                    'mautic.report.reports' => array(
                                        'route' => 'mautic_report_index',
                                        'iconClass' => 'fa-line-chart',
                                        'access' => array(
                                            0 => 'report:reports:viewown',
                                            1 => 'report:reports:viewother',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.report.search.subscriber' => array(
                                    'class' => 'Mautic\\ReportBundle\\EventListener\\SearchSubscriber',
                                ),
                                'mautic.report.report.subscriber' => array(
                                    'class' => 'Mautic\\ReportBundle\\EventListener\\ReportSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.form.type.report' => array(
                                    'class' => 'Mautic\\ReportBundle\\Form\\Type\\ReportType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'report',
                                ),
                                'mautic.form.type.filter_selector' => array(
                                    'class' => 'Mautic\\ReportBundle\\Form\\Type\\FilterSelectorType',
                                    'alias' => 'filter_selector',
                                ),
                                'mautic.form.type.table_order' => array(
                                    'class' => 'Mautic\\ReportBundle\\Form\\Type\\TableOrderType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'table_order',
                                ),
                                'mautic.form.type.report_filters' => array(
                                    'class' => 'Mautic\\ReportBundle\\Form\\Type\\ReportFiltersType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'report_filters',
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticPageBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Page',
                    'bundle' => 'PageBundle',
                    'namespace' => 'Mautic\\PageBundle',
                    'symfonyBundleName' => 'MauticPageBundle',
                    'bundleClass' => 'Mautic\\PageBundle\\MauticPageBundle',
                    'relative' => 'app/bundles/PageBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/PageBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_page_buildertoken_index' => array(
                                    'path' => '/pages/buildertokens/{page}',
                                    'controller' => 'MauticPageBundle:SubscribedEvents\\BuilderToken:index',
                                ),
                                'mautic_page_index' => array(
                                    'path' => '/pages/{page}',
                                    'controller' => 'MauticPageBundle:Page:index',
                                ),
                                'mautic_page_action' => array(
                                    'path' => '/pages/{objectAction}/{objectId}',
                                    'controller' => 'MauticPageBundle:Page:execute',
                                ),
                            ),
                            'public' => array(
                                'mautic_page_tracker' => array(
                                    'path' => '/mtracking.gif',
                                    'controller' => 'MauticPageBundle:Public:trackingImage',
                                ),
                                'mautic_page_trackable' => array(
                                    'path' => '/r/{redirectId}',
                                    'controller' => 'MauticPageBundle:Public:redirect',
                                ),
                                'mautic_page_redirect' => array(
                                    'path' => '/redirect/{redirectId}',
                                    'controller' => 'MauticPageBundle:Public:redirect',
                                ),
                                'mautic_page_preview' => array(
                                    'path' => '/page/preview/{id}',
                                    'controller' => 'MauticPageBundle:Public:preview',
                                ),
                            ),
                            'api' => array(
                                'mautic_api_getpages' => array(
                                    'path' => '/pages',
                                    'controller' => 'MauticPageBundle:Api\\PageApi:getEntities',
                                ),
                                'mautic_api_getpage' => array(
                                    'path' => '/pages/{id}',
                                    'controller' => 'MauticPageBundle:Api\\PageApi:getEntity',
                                ),
                            ),
                            'catchall' => array(
                                'mautic_page_public' => array(
                                    'path' => '/{slug}',
                                    'controller' => 'MauticPageBundle:Public:index',
                                    'requirements' => array(
                                        'slug' => '^(?!(_(profiler|wdt)|css|images|js|favicon.ico|apps/bundles/|plugins/|addons/)).+',
                                    ),
                                ),
                            ),
                        ),
                        'menu' => array(
                            'main' => array(
                                'priority' => 30,
                                'items' => array(
                                    'mautic.page.pages' => array(
                                        'route' => 'mautic_page_index',
                                        'id' => 'mautic_page_root',
                                        'iconClass' => 'fa-file-text-o',
                                        'access' => array(
                                            0 => 'page:pages:viewown',
                                            1 => 'page:pages:viewother',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        'categories' => array(
                            'page' => NULL,
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.page.subscriber' => array(
                                    'class' => 'Mautic\\PageBundle\\EventListener\\PageSubscriber',
                                ),
                                'mautic.pagebuilder.subscriber' => array(
                                    'class' => 'Mautic\\PageBundle\\EventListener\\BuilderSubscriber',
                                ),
                                'mautic.page.pointbundle.subscriber' => array(
                                    'class' => 'Mautic\\PageBundle\\EventListener\\PointSubscriber',
                                ),
                                'mautic.page.reportbundle.subscriber' => array(
                                    'class' => 'Mautic\\PageBundle\\EventListener\\ReportSubscriber',
                                ),
                                'mautic.page.campaignbundle.subscriber' => array(
                                    'class' => 'Mautic\\PageBundle\\EventListener\\CampaignSubscriber',
                                ),
                                'mautic.page.leadbundle.subscriber' => array(
                                    'class' => 'Mautic\\PageBundle\\EventListener\\LeadSubscriber',
                                ),
                                'mautic.page.calendarbundle.subscriber' => array(
                                    'class' => 'Mautic\\PageBundle\\EventListener\\CalendarSubscriber',
                                ),
                                'mautic.page.configbundle.subscriber' => array(
                                    'class' => 'Mautic\\PageBundle\\EventListener\\ConfigSubscriber',
                                ),
                                'mautic.page.search.subscriber' => array(
                                    'class' => 'Mautic\\PageBundle\\EventListener\\SearchSubscriber',
                                ),
                                'mautic.page.webhook.subscriber' => array(
                                    'class' => 'Mautic\\PageBundle\\EventListener\\WebhookSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.form.type.page' => array(
                                    'class' => 'Mautic\\PageBundle\\Form\\Type\\PageType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'page',
                                ),
                                'mautic.form.type.pagevariant' => array(
                                    'class' => 'Mautic\\PageBundle\\Form\\Type\\VariantType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'pagevariant',
                                ),
                                'mautic.form.type.pointaction_pointhit' => array(
                                    'class' => 'Mautic\\PageBundle\\Form\\Type\\PointActionPageHitType',
                                    'alias' => 'pointaction_pagehit',
                                ),
                                'mautic.form.type.pointaction_urlhit' => array(
                                    'class' => 'Mautic\\PageBundle\\Form\\Type\\PointActionUrlHitType',
                                    'alias' => 'pointaction_urlhit',
                                ),
                                'mautic.form.type.pagehit.campaign_trigger' => array(
                                    'class' => 'Mautic\\PageBundle\\Form\\Type\\CampaignEventPageHitType',
                                    'alias' => 'campaignevent_pagehit',
                                ),
                                'mautic.form.type.pagelist' => array(
                                    'class' => 'Mautic\\PageBundle\\Form\\Type\\PageListType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'page_list',
                                ),
                                'mautic.form.type.page_abtest_settings' => array(
                                    'class' => 'Mautic\\PageBundle\\Form\\Type\\AbTestPropertiesType',
                                    'alias' => 'page_abtest_settings',
                                ),
                                'mautic.form.type.page_publish_dates' => array(
                                    'class' => 'Mautic\\PageBundle\\Form\\Type\\PagePublishDatesType',
                                    'alias' => 'page_publish_dates',
                                ),
                                'mautic.form.type.pageconfig' => array(
                                    'class' => 'Mautic\\PageBundle\\Form\\Type\\ConfigType',
                                    'alias' => 'pageconfig',
                                ),
                                'mautic.form.type.slideshow_config' => array(
                                    'class' => 'Mautic\\PageBundle\\Form\\Type\\SlideshowGlobalConfigType',
                                    'alias' => 'slideshow_config',
                                ),
                                'mautic.form.type.slideshow_slide_config' => array(
                                    'class' => 'Mautic\\PageBundle\\Form\\Type\\SlideshowSlideConfigType',
                                    'alias' => 'slideshow_slide_config',
                                ),
                                'mautic.form.type.redirect_list' => array(
                                    'class' => 'Mautic\\PageBundle\\Form\\Type\\RedirectListType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'redirect_list',
                                ),
                            ),
                        ),
                        'parameters' => array(
                            'cat_in_page_url' => false,
                            'google_analytics' => false,
                            'redirect_list_types' => array(
                                301 => 'mautic.page.form.redirecttype.permanent',
                                302 => 'mautic.page.form.redirecttype.temporary',
                            ),
                        ),
                    ),
                ),
                'MauticCampaignBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Campaign',
                    'bundle' => 'CampaignBundle',
                    'namespace' => 'Mautic\\CampaignBundle',
                    'symfonyBundleName' => 'MauticCampaignBundle',
                    'bundleClass' => 'Mautic\\CampaignBundle\\MauticCampaignBundle',
                    'relative' => 'app/bundles/CampaignBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/CampaignBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_campaignevent_action' => array(
                                    'path' => '/campaigns/events/{objectAction}/{objectId}',
                                    'controller' => 'MauticCampaignBundle:Event:execute',
                                ),
                                'mautic_campaignsource_action' => array(
                                    'path' => '/campaigns/sources/{objectAction}/{objectId}',
                                    'controller' => 'MauticCampaignBundle:Source:execute',
                                ),
                                'mautic_campaign_index' => array(
                                    'path' => '/campaigns/{page}',
                                    'controller' => 'MauticCampaignBundle:Campaign:index',
                                ),
                                'mautic_campaign_leads' => array(
                                    'path' => '/campaigns/view/{objectId}/leads/{page}',
                                    'controller' => 'MauticCampaignBundle:Campaign:leads',
                                ),
                                'mautic_campaign_action' => array(
                                    'path' => '/campaigns/{objectAction}/{objectId}',
                                    'controller' => 'MauticCampaignBundle:Campaign:execute',
                                ),
                            ),
                            'api' => array(
                                'mautic_api_getcampaigns' => array(
                                    'path' => '/campaigns',
                                    'controller' => 'MauticCampaignBundle:Api\\CampaignApi:getEntities',
                                ),
                                'mautic_api_getcampaign' => array(
                                    'path' => '/campaigns/{id}',
                                    'controller' => 'MauticCampaignBundle:Api\\CampaignApi:getEntity',
                                ),
                                'mautic_api_campaignaddlead' => array(
                                    'path' => '/campaigns/{id}/lead/add/{leadId}',
                                    'controller' => 'MauticCampaignBundle:Api\\CampaignApi:addLead',
                                    'method' => 'POST',
                                ),
                                'mautic_api_campaignremovelead' => array(
                                    'path' => '/campaigns/{id}/lead/remove/{leadId}',
                                    'controller' => 'MauticCampaignBundle:Api\\CampaignApi:removeLead',
                                    'method' => 'POST',
                                ),
                            ),
                        ),
                        'menu' => array(
                            'main' => array(
                                'priority' => 10,
                                'items' => array(
                                    'mautic.campaign.campaigns' => array(
                                        'route' => 'mautic_campaign_index',
                                        'id' => 'mautic_campaigns_root',
                                        'iconClass' => 'fa-clock-o',
                                        'access' => 'campaign:campaigns:view',
                                    ),
                                ),
                            ),
                        ),
                        'categories' => array(
                            'campaign' => NULL,
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.campaign.subscriber' => array(
                                    'class' => 'Mautic\\CampaignBundle\\EventListener\\CampaignSubscriber',
                                ),
                                'mautic.campaign.leadbundle.subscriber' => array(
                                    'class' => 'Mautic\\CampaignBundle\\EventListener\\LeadSubscriber',
                                ),
                                'mautic.campaign.calendarbundle.subscriber' => array(
                                    'class' => 'Mautic\\CampaignBundle\\EventListener\\CalendarSubscriber',
                                ),
                                'mautic.campaign.pointbundle.subscriber' => array(
                                    'class' => 'Mautic\\CampaignBundle\\EventListener\\PointSubscriber',
                                ),
                                'mautic.campaign.search.subscriber' => array(
                                    'class' => 'Mautic\\CampaignBundle\\EventListener\\SearchSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.campaign.type.form' => array(
                                    'class' => 'Mautic\\CampaignBundle\\Form\\Type\\CampaignType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'campaign',
                                ),
                                'mautic.campaignrange.type.action' => array(
                                    'class' => 'Mautic\\CampaignBundle\\Form\\Type\\EventType',
                                    'alias' => 'campaignevent',
                                ),
                                'mautic.campaign.type.campaignlist' => array(
                                    'class' => 'Mautic\\CampaignBundle\\Form\\Type\\CampaignListType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'campaign_list',
                                ),
                                'mautic.campaign.type.trigger.leadchange' => array(
                                    'class' => 'Mautic\\CampaignBundle\\Form\\Type\\CampaignEventLeadChangeType',
                                    'alias' => 'campaignevent_leadchange',
                                ),
                                'mautic.campaign.type.action.addremovelead' => array(
                                    'class' => 'Mautic\\CampaignBundle\\Form\\Type\\CampaignEventAddRemoveLeadType',
                                    'alias' => 'campaignevent_addremovelead',
                                ),
                                'mautic.campaign.type.canvassettings' => array(
                                    'class' => 'Mautic\\CampaignBundle\\Form\\Type\\EventCanvasSettingsType',
                                    'alias' => 'campaignevent_canvassettings',
                                ),
                                'mautic.campaign.type.leadsource' => array(
                                    'class' => 'Mautic\\CampaignBundle\\Form\\Type\\CampaignLeadSourceType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'campaign_leadsource',
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticPointBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Point',
                    'bundle' => 'PointBundle',
                    'namespace' => 'Mautic\\PointBundle',
                    'symfonyBundleName' => 'MauticPointBundle',
                    'bundleClass' => 'Mautic\\PointBundle\\MauticPointBundle',
                    'relative' => 'app/bundles/PointBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/PointBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_pointtriggerevent_action' => array(
                                    'path' => '/points/triggers/events/{objectAction}/{objectId}',
                                    'controller' => 'MauticPointBundle:TriggerEvent:execute',
                                ),
                                'mautic_pointtrigger_index' => array(
                                    'path' => '/points/triggers/{page}',
                                    'controller' => 'MauticPointBundle:Trigger:index',
                                ),
                                'mautic_pointtrigger_action' => array(
                                    'path' => '/points/triggers/{objectAction}/{objectId}',
                                    'controller' => 'MauticPointBundle:Trigger:execute',
                                ),
                                'mautic_point_index' => array(
                                    'path' => '/points/{page}',
                                    'controller' => 'MauticPointBundle:Point:index',
                                ),
                                'mautic_point_action' => array(
                                    'path' => '/points/{objectAction}/{objectId}',
                                    'controller' => 'MauticPointBundle:Point:execute',
                                ),
                            ),
                            'api' => array(
                                'mautic_api_getpoints' => array(
                                    'path' => '/points',
                                    'controller' => 'MauticPointBundle:Api\\PointApi:getEntities',
                                ),
                                'mautic_api_getpoint' => array(
                                    'path' => '/points/{id}',
                                    'controller' => 'MauticPointBundle:Api\\PointApi:getEntity',
                                ),
                                'mautic_api_gettriggers' => array(
                                    'path' => '/points/triggers',
                                    'controller' => 'MauticPointBundle:Api\\TriggerApi:getEntities',
                                ),
                                'mautic_api_gettrigger' => array(
                                    'path' => '/points/triggers/{id}',
                                    'controller' => 'MauticPointBundle:Api\\TriggerApi:getEntity',
                                ),
                            ),
                        ),
                        'menu' => array(
                            'main' => array(
                                'priority' => 25,
                                'items' => array(
                                    'mautic.points.menu.root' => array(
                                        'id' => 'mautic_points_root',
                                        'iconClass' => 'fa-calculator',
                                        'access' => array(
                                            0 => 'point:points:view',
                                            1 => 'point:triggers:view',
                                        ),
                                        'children' => array(
                                            'mautic.point.menu.index' => array(
                                                'route' => 'mautic_point_index',
                                                'access' => 'point:points:view',
                                            ),
                                            'mautic.point.trigger.menu.index' => array(
                                                'route' => 'mautic_pointtrigger_index',
                                                'access' => 'point:triggers:view',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        'categories' => array(
                            'point' => NULL,
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.point.subscriber' => array(
                                    'class' => 'Mautic\\PointBundle\\EventListener\\PointSubscriber',
                                ),
                                'mautic.point.leadbundle.subscriber' => array(
                                    'class' => 'Mautic\\PointBundle\\EventListener\\LeadSubscriber',
                                ),
                                'mautic.point.search.subscriber' => array(
                                    'class' => 'Mautic\\PointBundle\\EventListener\\SearchSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.point.type.form' => array(
                                    'class' => 'Mautic\\PointBundle\\Form\\Type\\PointType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'point',
                                ),
                                'mautic.point.type.action' => array(
                                    'class' => 'Mautic\\PointBundle\\Form\\Type\\PointActionType',
                                    'alias' => 'pointaction',
                                ),
                                'mautic.pointtrigger.type.form' => array(
                                    'class' => 'Mautic\\PointBundle\\Form\\Type\\TriggerType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'pointtrigger',
                                ),
                                'mautic.pointtrigger.type.action' => array(
                                    'class' => 'Mautic\\PointBundle\\Form\\Type\\TriggerEventType',
                                    'alias' => 'pointtriggerevent',
                                ),
                                'mautic.point.type.genericpoint_settings' => array(
                                    'class' => 'Mautic\\PointBundle\\Form\\Type\\GenericPointSettingsType',
                                    'alias' => 'genericpoint_settings',
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticApiBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Api',
                    'bundle' => 'ApiBundle',
                    'namespace' => 'Mautic\\ApiBundle',
                    'symfonyBundleName' => 'MauticApiBundle',
                    'bundleClass' => 'Mautic\\ApiBundle\\MauticApiBundle',
                    'relative' => 'app/bundles/ApiBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/ApiBundle'),
                    'config' => array(
                        'routes' => array(
                            'public' => array(
                                'bazinga_oauth_server_requesttoken' => array(
                                    'path' => '/oauth/v1/request_token',
                                    'controller' => 'bazinga.oauth.controller.server:requestTokenAction',
                                    'method' => 'GET|POST',
                                ),
                                'bazinga_oauth_login_allow' => array(
                                    'path' => '/oauth/v1/authorize',
                                    'controller' => 'MauticApiBundle:oAuth1/Authorize:allow',
                                    'method' => 'GET',
                                ),
                                'bazinga_oauth_server_authorize' => array(
                                    'path' => '/oauth/v1/authorize',
                                    'controller' => 'bazinga.oauth.controller.server:authorizeAction',
                                    'method' => 'POST',
                                ),
                                'mautic_oauth1_server_auth_login' => array(
                                    'path' => '/oauth/v1/authorize_login',
                                    'controller' => 'MauticApiBundle:oAuth1/Security:login',
                                    'method' => 'GET|POST',
                                ),
                                'mautic_oauth1_server_auth_login_check' => array(
                                    'path' => '/oauth/v1/authorize_login_check',
                                    'controller' => 'MauticApiBundle:oAuth1/Security:loginCheck',
                                    'method' => 'GET|POST',
                                ),
                                'bazinga_oauth_server_accesstoken' => array(
                                    'path' => '/oauth/v1/access_token',
                                    'controller' => 'bazinga.oauth.controller.server:accessTokenAction',
                                    'method' => 'GET|POST',
                                ),
                                'fos_oauth_server_token' => array(
                                    'path' => '/oauth/v2/token',
                                    'controller' => 'fos_oauth_server.controller.token:tokenAction',
                                    'method' => 'GET|POST',
                                ),
                                'fos_oauth_server_authorize' => array(
                                    'path' => '/oauth/v2/authorize',
                                    'controller' => 'MauticApiBundle:oAuth2/Authorize:authorize',
                                    'method' => 'GET|POST',
                                ),
                                'mautic_oauth2_server_auth_login' => array(
                                    'path' => '/oauth/v2/authorize_login',
                                    'controller' => 'MauticApiBundle:oAuth2/Security:login',
                                    'method' => 'GET|POST',
                                ),
                                'mautic_oauth2_server_auth_login_check' => array(
                                    'path' => '/oauth/v2/authorize_login_check',
                                    'controller' => 'MauticApiBundle:oAuth2/Security:loginCheck',
                                    'method' => 'GET|POST',
                                ),
                            ),
                            'main' => array(
                                'mautic_client_index' => array(
                                    'path' => '/credentials/{page}',
                                    'controller' => 'MauticApiBundle:Client:index',
                                ),
                                'mautic_client_action' => array(
                                    'path' => '/credentials/{objectAction}/{objectId}',
                                    'controller' => 'MauticApiBundle:Client:execute',
                                ),
                            ),
                        ),
                        'menu' => array(
                            'admin' => array(
                                'items' => array(
                                    'mautic.api.client.menu.index' => array(
                                        'route' => 'mautic_client_index',
                                        'iconClass' => 'fa-puzzle-piece',
                                        'access' => 'api:clients:view',
                                        'checks' => array(
                                            'parameters' => array(
                                                'api_enabled' => true,
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.api.subscriber' => array(
                                    'class' => 'Mautic\\ApiBundle\\EventListener\\ApiSubscriber',
                                ),
                                'mautic.api.configbundle.subscriber' => array(
                                    'class' => 'Mautic\\ApiBundle\\EventListener\\ConfigSubscriber',
                                ),
                                'mautic.api.search.subscriber' => array(
                                    'class' => 'Mautic\\ApiBundle\\EventListener\\SearchSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.form.type.apiclients' => array(
                                    'class' => 'Mautic\\ApiBundle\\Form\\Type\\ClientType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'client',
                                ),
                                'mautic.form.type.apiconfig' => array(
                                    'class' => 'Mautic\\ApiBundle\\Form\\Type\\ConfigType',
                                    'alias' => 'apiconfig',
                                ),
                            ),
                            'other' => array(
                                'mautic.api.oauth.event_listener' => array(
                                    'class' => 'Mautic\\ApiBundle\\EventListener\\OAuthEventListener',
                                    'arguments' => 'mautic.factory',
                                    'tags' => array(
                                        0 => 'kernel.event_listener',
                                        1 => 'kernel.event_listener',
                                    ),
                                    'tagArguments' => array(
                                        0 => array(
                                            'event' => 'fos_oauth_server.pre_authorization_process',
                                            'method' => 'onPreAuthorizationProcess',
                                        ),
                                        1 => array(
                                            'event' => 'fos_oauth_server.post_authorization_process',
                                            'method' => 'onPostAuthorizationProcess',
                                        ),
                                    ),
                                ),
                                'mautic.api.oauth1.nonce_provider' => array(
                                    'class' => 'Mautic\\ApiBundle\\Provider\\NonceProvider',
                                    'arguments' => 'doctrine.orm.entity_manager',
                                ),
                                'bazinga.oauth.security.authentication.provider.class' => 'Mautic\\ApiBundle\\Security\\OAuth1\\Authentication\\Provider\\OAuthProvider',
                                'bazinga.oauth.security.authentication.listener.class' => 'Mautic\\ApiBundle\\Security\\OAuth1\\Firewall\\OAuthListener',
                                'bazinga.oauth.event_listener.request.class' => 'Mautic\\ApiBundle\\EventListener\\OAuth1\\OAuthRequestListener',
                                'fos_oauth_server.security.authentication.listener.class' => 'Mautic\\ApiBundle\\Security\\OAuth2\\Firewall\\OAuthListener',
                                'jms_serializer.metadata.annotation_driver.class' => 'Mautic\\ApiBundle\\Serializer\\Driver\\AnnotationDriver',
                                'jms_serializer.metadata.php_driver.class' => 'Mautic\\ApiBundle\\Serializer\\Driver\\ApiMetadataDriver',
                                'mautic.validator.oauthcallback' => array(
                                    'class' => 'Mautic\\ApiBundle\\Form\\Validator\\Constraints\\OAuthCallbackValidator',
                                    'tag' => 'validator.constraint_validator',
                                    'alias' => 'oauth_callback',
                                ),
                            ),
                        ),
                        'parameters' => array(
                            'api_enabled' => false,
                            'api_oauth2_access_token_lifetime' => 60,
                            'api_oauth2_refresh_token_lifetime' => 14,
                        ),
                    ),
                ),
                'MauticUserBundle' => array(
                    'isPlugin' => false,
                    'base' => 'User',
                    'bundle' => 'UserBundle',
                    'namespace' => 'Mautic\\UserBundle',
                    'symfonyBundleName' => 'MauticUserBundle',
                    'bundleClass' => 'Mautic\\UserBundle\\MauticUserBundle',
                    'relative' => 'app/bundles/UserBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/UserBundle'),
                    'config' => array(
                        'menu' => array(
                            'admin' => array(
                                'mautic.user.users' => array(
                                    'access' => 'user:users:view',
                                    'route' => 'mautic_user_index',
                                    'iconClass' => 'fa-users',
                                ),
                                'mautic.user.roles' => array(
                                    'access' => 'user:roles:view',
                                    'route' => 'mautic_role_index',
                                    'iconClass' => 'fa-lock',
                                ),
                            ),
                        ),
                        'routes' => array(
                            'main' => array(
                                'login' => array(
                                    'path' => '/login',
                                    'controller' => 'MauticUserBundle:Security:login',
                                ),
                                'mautic_user_logincheck' => array(
                                    'path' => '/login_check',
                                ),
                                'mautic_user_logout' => array(
                                    'path' => '/logout',
                                ),
                                'mautic_user_index' => array(
                                    'path' => '/users/{page}',
                                    'controller' => 'MauticUserBundle:User:index',
                                ),
                                'mautic_user_action' => array(
                                    'path' => '/users/{objectAction}/{objectId}',
                                    'controller' => 'MauticUserBundle:User:execute',
                                ),
                                'mautic_role_index' => array(
                                    'path' => '/roles/{page}',
                                    'controller' => 'MauticUserBundle:Role:index',
                                ),
                                'mautic_role_action' => array(
                                    'path' => '/roles/{objectAction}/{objectId}',
                                    'controller' => 'MauticUserBundle:Role:execute',
                                ),
                                'mautic_user_account' => array(
                                    'path' => '/account',
                                    'controller' => 'MauticUserBundle:Profile:index',
                                ),
                            ),
                            'api' => array(
                                'mautic_api_getusers' => array(
                                    'path' => '/users',
                                    'controller' => 'MauticUserBundle:Api\\UserApi:getEntities',
                                ),
                                'mautic_api_getuser' => array(
                                    'path' => '/users/{id}',
                                    'controller' => 'MauticUserBundle:Api\\UserApi:getEntity',
                                ),
                                'mautic_api_getself' => array(
                                    'path' => '/users/self',
                                    'controller' => 'MauticUserBundle:Api\\UserApi:getSelf',
                                ),
                                'mautic_api_checkpermission' => array(
                                    'path' => '/users/{id}/permissioncheck',
                                    'controller' => 'MauticUserBundle:Api\\UserApi:isGranted',
                                    'method' => 'POST',
                                ),
                                'mautic_api_getuserroles' => array(
                                    'path' => '/users/list/roles',
                                    'controller' => 'MauticUserBundle:Api\\UserApi:getRoles',
                                ),
                                'mautic_api_getroles' => array(
                                    'path' => '/roles',
                                    'controller' => 'MauticUserBundle:Api\\RoleApi:getEntities',
                                ),
                                'mautic_api_getrole' => array(
                                    'path' => '/roles/{id}',
                                    'controller' => 'MauticUserBundle:Api\\RoleApi:getEntity',
                                ),
                            ),
                            'public' => array(
                                'mautic_user_passwordreset' => array(
                                    'path' => '/passwordreset',
                                    'controller' => 'MauticUserBundle:Public:passwordReset',
                                ),
                            ),
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.user.subscriber' => array(
                                    'class' => 'Mautic\\UserBundle\\EventListener\\UserSubscriber',
                                ),
                                'mautic.user.search.subscriber' => array(
                                    'class' => 'Mautic\\UserBundle\\EventListener\\SearchSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.form.type.user' => array(
                                    'class' => 'Mautic\\UserBundle\\Form\\Type\\UserType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'user',
                                ),
                                'mautic.form.type.role' => array(
                                    'class' => 'Mautic\\UserBundle\\Form\\Type\\RoleType',
                                    'alias' => 'role',
                                ),
                                'mautic.form.type.permissions' => array(
                                    'class' => 'Mautic\\UserBundle\\Form\\Type\\PermissionsType',
                                    'alias' => 'permissions',
                                ),
                                'mautic.form.type.permissionlist' => array(
                                    'class' => 'Mautic\\UserBundle\\Form\\Type\\PermissionListType',
                                    'alias' => 'permissionlist',
                                ),
                                'mautic.form.type.passwordreset' => array(
                                    'class' => 'Mautic\\UserBundle\\Form\\Type\\PasswordResetType',
                                    'alias' => 'passwordreset',
                                ),
                                'mautic.form.type.user_list' => array(
                                    'class' => 'Mautic\\UserBundle\\Form\\Type\\UserListType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'user_list',
                                ),
                            ),
                            'other' => array(
                                'mautic.user.manager' => array(
                                    'class' => 'Doctrine\\ORM\\EntityManager',
                                    'arguments' => 'Mautic\\UserBundle\\Entity\\User',
                                    'factoryService' => 'doctrine',
                                    'factoryMethod' => 'getManagerForClass',
                                ),
                                'mautic.user.repository' => array(
                                    'class' => 'Mautic\\UserBundle\\Entity\\UserRepository',
                                    'arguments' => 'Mautic\\UserBundle\\Entity\\User',
                                    'factoryService' => 'mautic.user.manager',
                                    'factoryMethod' => 'getRepository',
                                ),
                                'mautic.permission.manager' => array(
                                    'class' => 'Doctrine\\ORM\\EntityManager',
                                    'arguments' => 'Mautic\\UserBundle\\Entity\\Permission',
                                    'factoryService' => 'doctrine',
                                    'factoryMethod' => 'getManagerForClass',
                                ),
                                'mautic.permission.repository' => array(
                                    'class' => 'Mautic\\UserBundle\\Entity\\PermissionRepository',
                                    'arguments' => 'Mautic\\UserBundle\\Entity\\Permission',
                                    'factoryService' => 'mautic.permission.manager',
                                    'factoryMethod' => 'getRepository',
                                ),
                                'mautic.user.provider' => array(
                                    'class' => 'Mautic\\UserBundle\\Security\\Provider\\UserProvider',
                                    'arguments' => array(
                                        0 => 'mautic.user.repository',
                                        1 => 'mautic.permission.repository',
                                        2 => 'session',
                                    ),
                                ),
                                'mautic.security.authentication_handler' => array(
                                    'class' => 'Mautic\\UserBundle\\Security\\Authentication\\AuthenticationHandler',
                                    'arguments' => array(
                                        0 => 'router',
                                        1 => 'session',
                                    ),
                                ),
                                'mautic.security.logout_handler' => array(
                                    'class' => 'Mautic\\UserBundle\\Security\\Authentication\\LogoutHandler',
                                    'arguments' => 'mautic.factory',
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticEmailBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Email',
                    'bundle' => 'EmailBundle',
                    'namespace' => 'Mautic\\EmailBundle',
                    'symfonyBundleName' => 'MauticEmailBundle',
                    'bundleClass' => 'Mautic\\EmailBundle\\MauticEmailBundle',
                    'relative' => 'app/bundles/EmailBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/EmailBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_email_index' => array(
                                    'path' => '/emails/{page}',
                                    'controller' => 'MauticEmailBundle:Email:index',
                                ),
                                'mautic_email_action' => array(
                                    'path' => '/emails/{objectAction}/{objectId}',
                                    'controller' => 'MauticEmailBundle:Email:execute',
                                ),
                            ),
                            'api' => array(
                                'mautic_api_getemails' => array(
                                    'path' => '/emails',
                                    'controller' => 'MauticEmailBundle:Api\\EmailApi:getEntities',
                                ),
                                'mautic_api_getemail' => array(
                                    'path' => '/emails/{id}',
                                    'controller' => 'MauticEmailBundle:Api\\EmailApi:getEntity',
                                ),
                                'mautic_api_sendleademail' => array(
                                    'path' => '/emails/{id}/send/lead/{leadId}',
                                    'controller' => 'MauticEmailBundle:Api\\EmailApi:sendLead',
                                    'method' => 'POST',
                                ),
                                'mautic_api_sendemail' => array(
                                    'path' => '/emails/{id}/send',
                                    'controller' => 'MauticEmailBundle:Api\\EmailApi:send',
                                    'method' => 'POST',
                                ),
                            ),
                            'public' => array(
                                'mautic_email_tracker' => array(
                                    'path' => '/email/{idHash}.gif',
                                    'controller' => 'MauticEmailBundle:Public:trackingImage',
                                ),
                                'mautic_email_webview' => array(
                                    'path' => '/email/view/{idHash}',
                                    'controller' => 'MauticEmailBundle:Public:index',
                                ),
                                'mautic_email_unsubscribe' => array(
                                    'path' => '/email/unsubscribe/{idHash}',
                                    'controller' => 'MauticEmailBundle:Public:unsubscribe',
                                ),
                                'mautic_email_resubscribe' => array(
                                    'path' => '/email/resubscribe/{idHash}',
                                    'controller' => 'MauticEmailBundle:Public:resubscribe',
                                ),
                                'mautic_mailer_transport_callback' => array(
                                    'path' => '/mailer/{transport}/callback',
                                    'controller' => 'MauticEmailBundle:Public:mailerCallback',
                                ),
                                'mautic_email_preview' => array(
                                    'path' => '/email/preview/{objectId}',
                                    'controller' => 'MauticEmailBundle:Public:preview',
                                ),
                            ),
                        ),
                        'menu' => array(
                            'main' => array(
                                'priority' => 15,
                                'items' => array(
                                    'mautic.email.emails' => array(
                                        'route' => 'mautic_email_index',
                                        'id' => 'mautic_email_root',
                                        'iconClass' => 'fa-send',
                                        'access' => array(
                                            0 => 'email:emails:viewown',
                                            1 => 'email:emails:viewother',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        'categories' => array(
                            'email' => NULL,
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.email.subscriber' => array(
                                    'class' => 'Mautic\\EmailBundle\\EventListener\\EmailSubscriber',
                                ),
                                'mautic.emailbuilder.subscriber' => array(
                                    'class' => 'Mautic\\EmailBundle\\EventListener\\BuilderSubscriber',
                                ),
                                'mautic.email.campaignbundle.subscriber' => array(
                                    'class' => 'Mautic\\EmailBundle\\EventListener\\CampaignSubscriber',
                                ),
                                'mautic.email.formbundle.subscriber' => array(
                                    'class' => 'Mautic\\EmailBundle\\EventListener\\FormSubscriber',
                                ),
                                'mautic.email.reportbundle.subscriber' => array(
                                    'class' => 'Mautic\\EmailBundle\\EventListener\\ReportSubscriber',
                                ),
                                'mautic.email.leadbundle.subscriber' => array(
                                    'class' => 'Mautic\\EmailBundle\\EventListener\\LeadSubscriber',
                                ),
                                'mautic.email.pointbundle.subscriber' => array(
                                    'class' => 'Mautic\\EmailBundle\\EventListener\\PointSubscriber',
                                ),
                                'mautic.email.calendarbundle.subscriber' => array(
                                    'class' => 'Mautic\\EmailBundle\\EventListener\\CalendarSubscriber',
                                ),
                                'mautic.email.search.subscriber' => array(
                                    'class' => 'Mautic\\EmailBundle\\EventListener\\SearchSubscriber',
                                ),
                                'mautic.email.webhook.subscriber' => array(
                                    'class' => 'Mautic\\EmailBundle\\EventListener\\WebhookSubscriber',
                                ),
                                'mautic.email.configbundle.subscriber' => array(
                                    'class' => 'Mautic\\EmailBundle\\EventListener\\ConfigSubscriber',
                                ),
                                'mautic.email.pagebundle.subscriber' => array(
                                    'class' => 'Mautic\\EmailBundle\\EventListener\\PageSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.form.type.email' => array(
                                    'class' => 'Mautic\\EmailBundle\\Form\\Type\\EmailType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'emailform',
                                ),
                                'mautic.form.type.emailvariant' => array(
                                    'class' => 'Mautic\\EmailBundle\\Form\\Type\\VariantType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'emailvariant',
                                ),
                                'mautic.form.type.email_list' => array(
                                    'class' => 'Mautic\\EmailBundle\\Form\\Type\\EmailListType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'email_list',
                                ),
                                'mautic.form.type.emailopen_list' => array(
                                    'class' => 'Mautic\\EmailBundle\\Form\\Type\\EmailOpenType',
                                    'alias' => 'emailopen_list',
                                ),
                                'mautic.form.type.emailsend_list' => array(
                                    'class' => 'Mautic\\EmailBundle\\Form\\Type\\EmailSendType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'emailsend_list',
                                ),
                                'mautic.form.type.formsubmit_sendemail_admin' => array(
                                    'class' => 'Mautic\\EmailBundle\\Form\\Type\\FormSubmitActionUserEmailType',
                                    'alias' => 'email_submitaction_useremail',
                                ),
                                'mautic.email.type.email_abtest_settings' => array(
                                    'class' => 'Mautic\\EmailBundle\\Form\\Type\\AbTestPropertiesType',
                                    'alias' => 'email_abtest_settings',
                                ),
                                'mautic.email.type.batch_send' => array(
                                    'class' => 'Mautic\\EmailBundle\\Form\\Type\\BatchSendType',
                                    'alias' => 'batch_send',
                                ),
                                'mautic.form.type.emailconfig' => array(
                                    'class' => 'Mautic\\EmailBundle\\Form\\Type\\ConfigType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'emailconfig',
                                ),
                                'mautic.form.type.coreconfig_monitored_mailboxes' => array(
                                    'class' => 'Mautic\\EmailBundle\\Form\\Type\\ConfigMonitoredMailboxesType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'monitored_mailboxes',
                                ),
                                'mautic.form.type.coreconfig_monitored_email' => array(
                                    'class' => 'Mautic\\EmailBundle\\Form\\Type\\ConfigMonitoredEmailType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'monitored_email',
                                ),
                            ),
                            'other' => array(
                                'mautic.validator.leadlistaccess' => array(
                                    'class' => 'Mautic\\LeadBundle\\Form\\Validator\\Constraints\\LeadListAccessValidator',
                                    'arguments' => 'mautic.factory',
                                    'tag' => 'validator.constraint_validator',
                                    'alias' => 'leadlist_access',
                                ),
                                'mautic.helper.mailbox' => array(
                                    'class' => 'Mautic\\EmailBundle\\MonitoredEmail\\Mailbox',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.helper.message' => array(
                                    'class' => 'Mautic\\EmailBundle\\Helper\\MessageHelper',
                                    'arguments' => 'mautic.factory',
                                ),
                                'mautic.transport.amazon' => array(
                                    'class' => 'Mautic\\EmailBundle\\Swiftmailer\\Transport\\AmazonTransport',
                                    'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                                    'methodCalls' => array(
                                        'setUsername' => array(
                                            0 => NULL,
                                        ),
                                        'setPassword' => array(
                                            0 => NULL,
                                        ),
                                    ),
                                ),
                                'mautic.transport.mandrill' => array(
                                    'class' => 'Mautic\\EmailBundle\\Swiftmailer\\Transport\\MandrillTransport',
                                    'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                                    'methodCalls' => array(
                                        'setUsername' => array(
                                            0 => NULL,
                                        ),
                                        'setPassword' => array(
                                            0 => NULL,
                                        ),
                                        'setMauticFactory' => array(
                                            0 => 'mautic.factory',
                                        ),
                                    ),
                                ),
                                'mautic.transport.sendgrid' => array(
                                    'class' => 'Mautic\\EmailBundle\\Swiftmailer\\Transport\\SendgridTransport',
                                    'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                                    'methodCalls' => array(
                                        'setUsername' => array(
                                            0 => NULL,
                                        ),
                                        'setPassword' => array(
                                            0 => NULL,
                                        ),
                                    ),
                                ),
                                'mautic.transport.postmark' => array(
                                    'class' => 'Mautic\\EmailBundle\\Swiftmailer\\Transport\\PostmarkTransport',
                                    'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                                    'methodCalls' => array(
                                        'setUsername' => array(
                                            0 => NULL,
                                        ),
                                        'setPassword' => array(
                                            0 => NULL,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        'parameters' => array(
                            'mailer_from_name' => 'Mautic',
                            'mailer_from_email' => 'email@yoursite.com',
                            'mailer_return_path' => NULL,
                            'mailer_transport' => 'mail',
                            'mailer_host' => '',
                            'mailer_port' => NULL,
                            'mailer_user' => NULL,
                            'mailer_password' => NULL,
                            'mailer_encryption' => NULL,
                            'mailer_auth_mode' => NULL,
                            'mailer_spool_type' => 'memory',
                            'mailer_spool_path' => ($this->targetDirs[2].'/spool'),
                            'mailer_spool_msg_limit' => NULL,
                            'mailer_spool_time_limit' => NULL,
                            'mailer_spool_recover_timeout' => 900,
                            'mailer_spool_clear_timeout' => 1800,
                            'unsubscribe_text' => NULL,
                            'webview_text' => NULL,
                            'unsubscribe_message' => NULL,
                            'resubscribe_message' => NULL,
                            'monitored_email' => array(
                            ),
                        ),
                    ),
                ),
                'MauticDashboardBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Dashboard',
                    'bundle' => 'DashboardBundle',
                    'namespace' => 'Mautic\\DashboardBundle',
                    'symfonyBundleName' => 'MauticDashboardBundle',
                    'bundleClass' => 'Mautic\\DashboardBundle\\MauticDashboardBundle',
                    'relative' => 'app/bundles/DashboardBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/DashboardBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_dashboard_index' => array(
                                    'path' => '/dashboard',
                                    'controller' => 'MauticDashboardBundle:Default:index',
                                ),
                            ),
                        ),
                        'menu' => array(
                            'main' => array(
                                'priority' => -100,
                                'items' => array(
                                    'mautic.dashboard.menu.index' => array(
                                        'route' => 'mautic_dashboard_index',
                                        'iconClass' => 'fa-th-large',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticPluginBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Plugin',
                    'bundle' => 'PluginBundle',
                    'namespace' => 'Mautic\\PluginBundle',
                    'symfonyBundleName' => 'MauticPluginBundle',
                    'bundleClass' => 'Mautic\\PluginBundle\\MauticPluginBundle',
                    'relative' => 'app/bundles/PluginBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/PluginBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_integration_auth_callback_bc' => array(
                                    'path' => '/addon/integrations/authcallback/{integration}',
                                    'controller' => 'MauticPluginBundle:Auth:authCallback',
                                ),
                                'mautic_integration_auth_callback' => array(
                                    'path' => '/plugins/integrations/authcallback/{integration}',
                                    'controller' => 'MauticPluginBundle:Auth:authCallback',
                                ),
                                'mautic_integration_auth_postauth' => array(
                                    'path' => '/plugins/integrations/authstatus',
                                    'controller' => 'MauticPluginBundle:Auth:authStatus',
                                ),
                                'mautic_plugin_index' => array(
                                    'path' => '/plugins',
                                    'controller' => 'MauticPluginBundle:Plugin:index',
                                ),
                                'mautic_plugin_config' => array(
                                    'path' => '/plugins/config/{name}',
                                    'controller' => 'MauticPluginBundle:Plugin:config',
                                ),
                                'mautic_plugin_info' => array(
                                    'path' => '/plugins/info/{name}',
                                    'controller' => 'MauticPluginBundle:Plugin:info',
                                ),
                                'mautic_plugin_reload' => array(
                                    'path' => '/plugins/reload',
                                    'controller' => 'MauticPluginBundle:Plugin:reload',
                                ),
                            ),
                        ),
                        'menu' => array(
                            'admin' => array(
                                'priority' => 50,
                                'items' => array(
                                    'mautic.plugin.plugins' => array(
                                        'id' => 'mautic_plugin_root',
                                        'iconClass' => 'fa-plus-circle',
                                        'access' => 'plugin:plugins:manage',
                                        'route' => 'mautic_plugin_index',
                                    ),
                                ),
                            ),
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.plugin.pointbundle.subscriber' => array(
                                    'class' => 'Mautic\\PluginBundle\\EventListener\\PointSubscriber',
                                ),
                                'mautic.plugin.formbundle.subscriber' => array(
                                    'class' => 'Mautic\\PluginBundle\\EventListener\\FormSubscriber',
                                ),
                                'mautic.plugin.campaignbundle.subscriber' => array(
                                    'class' => 'Mautic\\PluginBundle\\EventListener\\CampaignSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.form.type.integration.details' => array(
                                    'class' => 'Mautic\\PluginBundle\\Form\\Type\\DetailsType',
                                    'alias' => 'integration_details',
                                ),
                                'mautic.form.type.integration.settings' => array(
                                    'class' => 'Mautic\\PluginBundle\\Form\\Type\\FeatureSettingsType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'integration_featuresettings',
                                ),
                                'mautic.form.type.integration.fields' => array(
                                    'class' => 'Mautic\\PluginBundle\\Form\\Type\\FieldsType',
                                    'alias' => 'integration_fields',
                                ),
                                'mautic.form.type.integration.keys' => array(
                                    'class' => 'Mautic\\PluginBundle\\Form\\Type\\KeysType',
                                    'alias' => 'integration_keys',
                                ),
                                'mautic.form.type.integration.list' => array(
                                    'class' => 'Mautic\\PluginBundle\\Form\\Type\\IntegrationsListType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'integration_list',
                                ),
                                'mautic.form.type.integration.config' => array(
                                    'class' => 'Mautic\\PluginBundle\\Form\\Type\\IntegrationConfigType',
                                    'alias' => 'integration_config',
                                ),
                            ),
                            'other' => array(
                                'mautic.helper.integration' => array(
                                    'class' => 'Mautic\\PluginBundle\\Helper\\IntegrationHelper',
                                    'arguments' => 'mautic.factory',
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticInstallBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Install',
                    'bundle' => 'InstallBundle',
                    'namespace' => 'Mautic\\InstallBundle',
                    'symfonyBundleName' => 'MauticInstallBundle',
                    'bundleClass' => 'Mautic\\InstallBundle\\MauticInstallBundle',
                    'relative' => 'app/bundles/InstallBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/InstallBundle'),
                    'config' => array(
                        'routes' => array(
                            'public' => array(
                                'mautic_installer_home' => array(
                                    'path' => '/installer',
                                    'controller' => 'MauticInstallBundle:Install:step',
                                ),
                                'mautic_installer_remove_slash' => array(
                                    'path' => '/installer/',
                                    'controller' => 'MauticCoreBundle:Common:removeTrailingSlash',
                                ),
                                'mautic_installer_step' => array(
                                    'path' => '/installer/step/{index}',
                                    'controller' => 'MauticInstallBundle:Install:step',
                                ),
                                'mautic_installer_final' => array(
                                    'path' => '/installer/final',
                                    'controller' => 'MauticInstallBundle:Install:final',
                                ),
                            ),
                        ),
                        'services' => array(
                            'other' => array(
                                'mautic.configurator' => array(
                                    'class' => 'Mautic\\InstallBundle\\Configurator\\Configurator',
                                    'arguments' => array(
                                        0 => $this->targetDirs[2],
                                        1 => 'mautic.factory',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticConfigBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Config',
                    'bundle' => 'ConfigBundle',
                    'namespace' => 'Mautic\\ConfigBundle',
                    'symfonyBundleName' => 'MauticConfigBundle',
                    'bundleClass' => 'Mautic\\ConfigBundle\\MauticConfigBundle',
                    'relative' => 'app/bundles/ConfigBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/ConfigBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_config_action' => array(
                                    'path' => '/config/{objectAction}',
                                    'controller' => 'MauticConfigBundle:Config:execute',
                                ),
                                'mautic_sysinfo_index' => array(
                                    'path' => '/sysinfo',
                                    'controller' => 'MauticConfigBundle:Sysinfo:index',
                                ),
                            ),
                        ),
                        'menu' => array(
                            'admin' => array(
                                'mautic.config.menu.index' => array(
                                    'route' => 'mautic_config_action',
                                    'routeParameters' => array(
                                        'objectAction' => 'edit',
                                    ),
                                    'iconClass' => 'fa-cogs',
                                    'id' => 'mautic_config_index',
                                    'access' => 'admin',
                                ),
                                'mautic.sysinfo.menu.index' => array(
                                    'route' => 'mautic_sysinfo_index',
                                    'iconClass' => 'fa-life-ring',
                                    'id' => 'mautic_sysinfo_index',
                                    'access' => 'admin',
                                    'checks' => array(
                                        'parameters' => array(
                                            'sysinfo_disabled' => false,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.config.subscriber' => array(
                                    'class' => 'Mautic\\ConfigBundle\\EventListener\\ConfigSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.form.type.config' => array(
                                    'class' => 'Mautic\\ConfigBundle\\Form\\Type\\ConfigType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'config',
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticWebhookBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Webhook',
                    'bundle' => 'WebhookBundle',
                    'namespace' => 'Mautic\\WebhookBundle',
                    'symfonyBundleName' => 'MauticWebhookBundle',
                    'bundleClass' => 'Mautic\\WebhookBundle\\MauticWebhookBundle',
                    'relative' => 'app/bundles/WebhookBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/WebhookBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_webhook_index' => array(
                                    'path' => '/webhooks/{page}',
                                    'controller' => 'MauticWebhookBundle:Webhook:index',
                                ),
                                'mautic_webhook_action' => array(
                                    'path' => '/webhooks/{objectAction}/{objectId}',
                                    'controller' => 'MauticWebhookBundle:Webhook:execute',
                                ),
                            ),
                        ),
                        'menu' => array(
                            'admin' => array(
                                'items' => array(
                                    'mautic.webhook.webhooks' => array(
                                        'id' => 'mautic_webhook_root',
                                        'iconClass' => 'fa-exchange',
                                        'access' => array(
                                            0 => 'webhook:webhooks:viewown',
                                            1 => 'webhook:webhooks:viewother',
                                        ),
                                        'route' => 'mautic_webhook_index',
                                    ),
                                ),
                            ),
                        ),
                        'services' => array(
                            'forms' => array(
                                'mautic.form.type.webhook' => array(
                                    'class' => 'Mautic\\WebhookBundle\\Form\\Type\\WebhookType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'webhook',
                                ),
                                'mautic.form.type.webhookconfig' => array(
                                    'class' => 'Mautic\\WebhookBundle\\Form\\Type\\ConfigType',
                                    'alias' => 'webhookconfig',
                                ),
                            ),
                            'events' => array(
                                'mautic.webhook.lead.subscriber' => array(
                                    'class' => 'Mautic\\WebhookBundle\\EventListener\\LeadSubscriber',
                                ),
                                'mautic.webhook.form.subscriber' => array(
                                    'class' => 'Mautic\\WebhookBundle\\EventListener\\FormSubscriber',
                                ),
                                'mautic.webhook.email.subscriber' => array(
                                    'class' => 'Mautic\\WebhookBundle\\EventListener\\EmailSubscriber',
                                ),
                                'mautic.webhook.page.hit.subscriber' => array(
                                    'class' => 'Mautic\\WebhookBundle\\EventListener\\PageSubscriber',
                                ),
                                'mautic.webhook.config.subscriber' => array(
                                    'class' => 'Mautic\\WebhookBundle\\EventListener\\ConfigSubscriber',
                                ),
                                'mautic.webhook.audit.subscriber' => array(
                                    'class' => 'Mautic\\WebhookBundle\\EventListener\\WebhookSubscriber',
                                ),
                            ),
                        ),
                        'parameters' => array(
                            'webhook_start' => 0,
                            'webhook_limit' => 1000,
                            'webhook_log_max' => 10,
                            'queue_mode' => 'immediate_process',
                        ),
                    ),
                ),
                'MauticCalendarBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Calendar',
                    'bundle' => 'CalendarBundle',
                    'namespace' => 'Mautic\\CalendarBundle',
                    'symfonyBundleName' => 'MauticCalendarBundle',
                    'bundleClass' => 'Mautic\\CalendarBundle\\MauticCalendarBundle',
                    'relative' => 'app/bundles/CalendarBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/CalendarBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_calendar_index' => array(
                                    'path' => '/calendar',
                                    'controller' => 'MauticCalendarBundle:Default:index',
                                ),
                                'mautic_calendar_action' => array(
                                    'path' => '/calendar/{objectAction}',
                                    'controller' => 'MauticCalendarBundle:Default:execute',
                                ),
                            ),
                        ),
                        'menu' => array(
                            'main' => array(
                                'priority' => 1,
                                'items' => array(
                                    'mautic.calendar.menu.index' => array(
                                        'route' => 'mautic_calendar_index',
                                        'iconClass' => 'fa-calendar',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticAssetBundle' => array(
                    'isPlugin' => false,
                    'base' => 'Asset',
                    'bundle' => 'AssetBundle',
                    'namespace' => 'Mautic\\AssetBundle',
                    'symfonyBundleName' => 'MauticAssetBundle',
                    'bundleClass' => 'Mautic\\AssetBundle\\MauticAssetBundle',
                    'relative' => 'app/bundles/AssetBundle',
                    'directory' => ($this->targetDirs[2].'/bundles/AssetBundle'),
                    'config' => array(
                        'routes' => array(
                            'main' => array(
                                'mautic_asset_buildertoken_index' => array(
                                    'path' => '/asset/buildertokens/{page}',
                                    'controller' => 'MauticAssetBundle:SubscribedEvents\\BuilderToken:index',
                                ),
                                'mautic_asset_index' => array(
                                    'path' => '/assets/{page}',
                                    'controller' => 'MauticAssetBundle:Asset:index',
                                ),
                                'mautic_asset_remote' => array(
                                    'path' => '/assets/remote',
                                    'controller' => 'MauticAssetBundle:Asset:remote',
                                ),
                                'mautic_asset_action' => array(
                                    'path' => '/assets/{objectAction}/{objectId}',
                                    'controller' => 'MauticAssetBundle:Asset:execute',
                                ),
                            ),
                            'api' => array(
                                'mautic_api_getassets' => array(
                                    'path' => '/assets',
                                    'controller' => 'MauticAssetBundle:Api\\AssetApi:getEntities',
                                ),
                                'mautic_api_getasset' => array(
                                    'path' => '/assets/{id}',
                                    'controller' => 'MauticAssetBundle:Api\\AssetApi:getEntity',
                                ),
                            ),
                            'public' => array(
                                'mautic_asset_download' => array(
                                    'path' => '/asset/{slug}',
                                    'controller' => 'MauticAssetBundle:Public:download',
                                    'defaults' => array(
                                        'slug' => '',
                                    ),
                                ),
                            ),
                        ),
                        'menu' => array(
                            'main' => array(
                                'priority' => 35,
                                'items' => array(
                                    'mautic.asset.assets' => array(
                                        'route' => 'mautic_asset_index',
                                        'id' => 'mautic_asset_root',
                                        'iconClass' => 'fa-folder-open-o',
                                        'access' => array(
                                            0 => 'asset:assets:viewown',
                                            1 => 'asset:assets:viewother',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        'categories' => array(
                            'asset' => NULL,
                        ),
                        'services' => array(
                            'events' => array(
                                'mautic.asset.subscriber' => array(
                                    'class' => 'Mautic\\AssetBundle\\EventListener\\AssetSubscriber',
                                ),
                                'mautic.asset.pointbundle.subscriber' => array(
                                    'class' => 'Mautic\\AssetBundle\\EventListener\\PointSubscriber',
                                ),
                                'mautic.asset.formbundle.subscriber' => array(
                                    'class' => 'Mautic\\AssetBundle\\EventListener\\FormSubscriber',
                                ),
                                'mautic.asset.campaignbundle.subscriber' => array(
                                    'class' => 'Mautic\\AssetBundle\\EventListener\\CampaignSubscriber',
                                ),
                                'mautic.asset.reportbundle.subscriber' => array(
                                    'class' => 'Mautic\\AssetBundle\\EventListener\\ReportSubscriber',
                                ),
                                'mautic.asset.builder.subscriber' => array(
                                    'class' => 'Mautic\\AssetBundle\\EventListener\\BuilderSubscriber',
                                ),
                                'mautic.asset.leadbundle.subscriber' => array(
                                    'class' => 'Mautic\\AssetBundle\\EventListener\\LeadSubscriber',
                                ),
                                'mautic.asset.pagebundle.subscriber' => array(
                                    'class' => 'Mautic\\AssetBundle\\EventListener\\PageSubscriber',
                                ),
                                'mautic.asset.emailbundle.subscriber' => array(
                                    'class' => 'Mautic\\AssetBundle\\EventListener\\EmailSubscriber',
                                ),
                                'mautic.asset.configbundle.subscriber' => array(
                                    'class' => 'Mautic\\AssetBundle\\EventListener\\ConfigSubscriber',
                                ),
                                'mautic.asset.search.subscriber' => array(
                                    'class' => 'Mautic\\AssetBundle\\EventListener\\SearchSubscriber',
                                ),
                                'oneup_uploader.pre_upload' => array(
                                    'class' => 'Mautic\\AssetBundle\\EventListener\\UploadSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.form.type.asset' => array(
                                    'class' => 'Mautic\\AssetBundle\\Form\\Type\\AssetType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'asset',
                                ),
                                'mautic.form.type.pointaction_assetdownload' => array(
                                    'class' => 'Mautic\\AssetBundle\\Form\\Type\\PointActionAssetDownloadType',
                                    'alias' => 'pointaction_assetdownload',
                                ),
                                'mautic.form.type.campaignevent_assetdownload' => array(
                                    'class' => 'Mautic\\AssetBundle\\Form\\Type\\CampaignEventAssetDownloadType',
                                    'alias' => 'campaignevent_assetdownload',
                                ),
                                'mautic.form.type.formsubmit_assetdownload' => array(
                                    'class' => 'Mautic\\AssetBundle\\Form\\Type\\FormSubmitActionDownloadFileType',
                                    'alias' => 'asset_submitaction_downloadfile',
                                ),
                                'mautic.form.type.assetlist' => array(
                                    'class' => 'Mautic\\AssetBundle\\Form\\Type\\AssetListType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'asset_list',
                                ),
                                'mautic.form.type.assetconfig' => array(
                                    'class' => 'Mautic\\AssetBundle\\Form\\Type\\ConfigType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'assetconfig',
                                ),
                            ),
                            'others' => array(
                                'mautic.asset.upload.error.handler' => array(
                                    'class' => 'Mautic\\AssetBundle\\ErrorHandler\\DropzoneErrorHandler',
                                    'arguments' => 'mautic.factory',
                                ),
                                'oneup_uploader.controller.dropzone.class' => 'Mautic\\AssetBundle\\Controller\\UploadController',
                            ),
                        ),
                        'parameters' => array(
                            'upload_dir' => ($this->targetDirs[2].'/../media/files'),
                            'max_size' => '6',
                            'allowed_extensions' => array(
                                0 => 'csv',
                                1 => 'doc',
                                2 => 'docx',
                                3 => 'epub',
                                4 => 'gif',
                                5 => 'jpg',
                                6 => 'jpeg',
                                7 => 'mpg',
                                8 => 'mpeg',
                                9 => 'mp3',
                                10 => 'odt',
                                11 => 'odp',
                                12 => 'ods',
                                13 => 'pdf',
                                14 => 'png',
                                15 => 'ppt',
                                16 => 'pptx',
                                17 => 'tif',
                                18 => 'tiff',
                                19 => 'txt',
                                20 => 'xls',
                                21 => 'xlsx',
                                22 => 'wav',
                            ),
                        ),
                    ),
                ),
            ),
            'mautic.plugin.bundles' => array(
                'MauticCrmBundle' => array(
                    'isPlugin' => true,
                    'base' => 'MauticCrm',
                    'bundle' => 'MauticCrmBundle',
                    'namespace' => 'MauticPlugin\\MauticCrmBundle',
                    'symfonyBundleName' => 'MauticCrmBundle',
                    'bundleClass' => 'MauticPlugin\\MauticCrmBundle\\MauticCrmBundle',
                    'relative' => 'plugins/MauticCrmBundle',
                    'directory' => ($this->targetDirs[3].'/plugins/MauticCrmBundle'),
                    'config' => array(
                        'name' => 'CRM',
                        'description' => 'Enables integration with Mautic supported CRMs.',
                        'version' => '1.0',
                        'author' => 'Mautic',
                    ),
                ),
                'MauticEmailMarketingBundle' => array(
                    'isPlugin' => true,
                    'base' => 'MauticEmailMarketing',
                    'bundle' => 'MauticEmailMarketingBundle',
                    'namespace' => 'MauticPlugin\\MauticEmailMarketingBundle',
                    'symfonyBundleName' => 'MauticEmailMarketingBundle',
                    'bundleClass' => 'MauticPlugin\\MauticEmailMarketingBundle\\MauticEmailMarketingBundle',
                    'relative' => 'plugins/MauticEmailMarketingBundle',
                    'directory' => ($this->targetDirs[3].'/plugins/MauticEmailMarketingBundle'),
                    'config' => array(
                        'name' => 'Email Marketing',
                        'description' => 'Enables integration with Mautic supported email marketing services.',
                        'version' => '1.0',
                        'author' => 'Mautic',
                        'services' => array(
                            'forms' => array(
                                'mautic.form.type.emailmarketing.mailchimp' => array(
                                    'class' => 'MauticPlugin\\MauticEmailMarketingBundle\\Form\\Type\\MailchimpType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'emailmarketing_mailchimp',
                                ),
                                'mautic.form.type.emailmarketing.constantcontact' => array(
                                    'class' => 'MauticPlugin\\MauticEmailMarketingBundle\\Form\\Type\\ConstantContactType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'emailmarketing_constantcontact',
                                ),
                                'mautic.form.type.emailmarketing.icontact' => array(
                                    'class' => 'MauticPlugin\\MauticEmailMarketingBundle\\Form\\Type\\IcontactType',
                                    'arguments' => 'mautic.factory',
                                    'alias' => 'emailmarketing_icontact',
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticCloudStorageBundle' => array(
                    'isPlugin' => true,
                    'base' => 'MauticCloudStorage',
                    'bundle' => 'MauticCloudStorageBundle',
                    'namespace' => 'MauticPlugin\\MauticCloudStorageBundle',
                    'symfonyBundleName' => 'MauticCloudStorageBundle',
                    'bundleClass' => 'MauticPlugin\\MauticCloudStorageBundle\\MauticCloudStorageBundle',
                    'relative' => 'plugins/MauticCloudStorageBundle',
                    'directory' => ($this->targetDirs[3].'/plugins/MauticCloudStorageBundle'),
                    'config' => array(
                        'name' => 'Cloud Storage',
                        'description' => 'Enables integrations with Mautic supported cloud storage services.',
                        'version' => '1.0',
                        'author' => 'Mautic',
                        'services' => array(
                            'events' => array(
                                'mautic.cloudstorage.remoteassetbrowse.subscriber' => array(
                                    'class' => 'MauticPlugin\\MauticCloudStorageBundle\\EventListener\\RemoteAssetBrowseSubscriber',
                                ),
                            ),
                            'forms' => array(
                                'mautic.form.type.cloudstorage.openstack' => array(
                                    'class' => 'MauticPlugin\\MauticCloudStorageBundle\\Form\\Type\\OpenStackType',
                                    'alias' => 'cloudstorage_openstack',
                                ),
                                'mautic.form.type.cloudstorage.rackspace' => array(
                                    'class' => 'MauticPlugin\\MauticCloudStorageBundle\\Form\\Type\\RackspaceType',
                                    'alias' => 'cloudstorage_rackspace',
                                ),
                            ),
                        ),
                    ),
                ),
                'MauticSocialBundle' => array(
                    'isPlugin' => true,
                    'base' => 'MauticSocial',
                    'bundle' => 'MauticSocialBundle',
                    'namespace' => 'MauticPlugin\\MauticSocialBundle',
                    'symfonyBundleName' => 'MauticSocialBundle',
                    'bundleClass' => 'MauticPlugin\\MauticSocialBundle\\MauticSocialBundle',
                    'relative' => 'plugins/MauticSocialBundle',
                    'directory' => ($this->targetDirs[3].'/plugins/MauticSocialBundle'),
                    'config' => array(
                        'name' => 'Social Media',
                        'description' => 'Enables integrations with Mautic supported social media services.',
                        'version' => '1.0',
                        'author' => 'Mautic',
                        'services' => array(
                            'forms' => array(
                                'mautic.form.type.social.facebook' => array(
                                    'class' => 'MauticPlugin\\MauticSocialBundle\\Form\\Type\\FacebookType',
                                    'alias' => 'socialmedia_facebook',
                                ),
                                'mautic.form.type.social.twitter' => array(
                                    'class' => 'MauticPlugin\\MauticSocialBundle\\Form\\Type\\TwitterType',
                                    'alias' => 'socialmedia_twitter',
                                ),
                                'mautic.form.type.social.googleplus' => array(
                                    'class' => 'MauticPlugin\\MauticSocialBundle\\Form\\Type\\GooglePlusType',
                                    'alias' => 'socialmedia_googleplus',
                                ),
                                'mautic.form.type.social.linkedin' => array(
                                    'class' => 'MauticPlugin\\MauticSocialBundle\\Form\\Type\\LinkedInType',
                                    'alias' => 'socialmedia_linkedin',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'mautic.site_url' => 'http://ma.labots.co',
            'mautic.webroot' => NULL,
            'mautic.cache_path' => $this->targetDirs[1],
            'mautic.log_path' => ($this->targetDirs[2].'/logs'),
            'mautic.image_path' => 'media/images',
            'mautic.theme' => 'Mauve',
            'mautic.db_driver' => 'pdo_mysql',
            'mautic.db_host' => 'localhost',
            'mautic.db_port' => '3306',
            'mautic.db_name' => 'autoMarket',
            'mautic.db_user' => 'root',
            'mautic.db_password' => 'labots.co',
            'mautic.db_table_prefix' => 'ma',
            'mautic.db_path' => NULL,
            'mautic.locale' => 'ja',
            'mautic.secret_key' => '4c8d4301b58c8edcdd609e87134e4f8a63be002c3a3014a22f664d3b4e706f5a',
            'mautic.trusted_hosts' => array(
            ),
            'mautic.trusted_proxies' => array(
            ),
            'mautic.rememberme_key' => 'ab90c625f80f8126309fec4fae1da7fff78001dd',
            'mautic.rememberme_lifetime' => '31536000',
            'mautic.rememberme_path' => '/',
            'mautic.rememberme_domain' => NULL,
            'mautic.default_pagelimit' => 30,
            'mautic.default_timezone' => 'Asia/Tokyo',
            'mautic.date_format_full' => 'F j, Y g:i a T',
            'mautic.date_format_short' => 'D, M d',
            'mautic.date_format_dateonly' => 'F j, Y',
            'mautic.date_format_timeonly' => 'g:i a',
            'mautic.ip_lookup_service' => 'telize',
            'mautic.ip_lookup_auth' => NULL,
            'mautic.transifex_username' => '',
            'mautic.transifex_password' => '',
            'mautic.update_stability' => 'stable',
            'mautic.cookie_path' => '/',
            'mautic.cookie_domain' => NULL,
            'mautic.cookie_secure' => false,
            'mautic.cookie_httponly' => false,
            'mautic.do_not_track_ips' => array(
            ),
            'mautic.cat_in_page_url' => false,
            'mautic.google_analytics' => NULL,
            'mautic.redirect_list_types' => array(
                0 => 'mautic.page.form.redirecttype.permanent',
                1 => 'mautic.page.form.redirecttype.temporary',
            ),
            'mautic.api_enabled' => false,
            'mautic.api_oauth2_access_token_lifetime' => 60,
            'mautic.api_oauth2_refresh_token_lifetime' => 14,
            'mautic.mailer_from_name' => 'labots co',
            'mautic.mailer_from_email' => 'labots.co@gmail.com',
            'mautic.mailer_return_path' => NULL,
            'mautic.mailer_transport' => 'mail',
            'mautic.mailer_host' => NULL,
            'mautic.mailer_port' => NULL,
            'mautic.mailer_user' => NULL,
            'mautic.mailer_password' => NULL,
            'mautic.mailer_encryption' => NULL,
            'mautic.mailer_auth_mode' => NULL,
            'mautic.mailer_spool_type' => 'memory',
            'mautic.mailer_spool_path' => ($this->targetDirs[2].'/spool'),
            'mautic.mailer_spool_msg_limit' => NULL,
            'mautic.mailer_spool_time_limit' => NULL,
            'mautic.mailer_spool_recover_timeout' => '900',
            'mautic.mailer_spool_clear_timeout' => '1800',
            'mautic.unsubscribe_text' => '<a href=\'|URL|\'>Unsubscribe</a> to no longer receive emails from us.',
            'mautic.webview_text' => '<a href=\'|URL|\'>Having trouble reading this email? Click here.</a>',
            'mautic.unsubscribe_message' => 'We are sorry to see you go! |EMAIL| will no longer receive emails from us. If this was by mistake, <a href=\'|URL|\'>click here to re-subscribe</a>.',
            'mautic.resubscribe_message' => '|EMAIL| has been re-subscribed. If this was by mistake, <a href=\'|URL|\'>click here to unsubscribe</a>.',
            'mautic.monitored_email' => array(
                'general' => array(
                    'address' => '',
                    'host' => '',
                    'port' => '993',
                    'encryption' => '/ssl',
                    'user' => '',
                    'password' => '',
                ),
                'EmailBundle_bounces' => array(
                    'address' => '',
                    'host' => '',
                    'port' => '993',
                    'encryption' => '/ssl',
                    'user' => '',
                    'password' => '',
                    'override_settings' => '',
                    'folder' => '',
                    'ssl' => '1',
                ),
                'EmailBundle_unsubscribes' => array(
                    'address' => '',
                    'host' => '',
                    'port' => '993',
                    'encryption' => '/ssl',
                    'user' => '',
                    'password' => '',
                    'override_settings' => '',
                    'folder' => '',
                    'ssl' => '1',
                ),
            ),
            'mautic.webhook_start' => 0,
            'mautic.webhook_limit' => 1000,
            'mautic.webhook_log_max' => 10,
            'mautic.queue_mode' => 'immediate_process',
            'mautic.upload_dir' => ($this->targetDirs[2].'/../media/files'),
            'mautic.max_size' => '6',
            'mautic.allowed_extensions' => array(
                0 => 'csv',
                1 => 'doc',
                2 => 'docx',
                3 => 'epub',
                4 => 'gif',
                5 => 'jpg',
                6 => 'jpeg',
                7 => 'mpg',
                8 => 'mpeg',
                9 => 'mp3',
                10 => 'odt',
                11 => 'odp',
                12 => 'ods',
                13 => 'pdf',
                14 => 'png',
                15 => 'ppt',
                16 => 'pptx',
                17 => 'tif',
                18 => 'tiff',
                19 => 'txt',
                20 => 'xls',
                21 => 'xlsx',
                22 => 'wav',
            ),
            'mautic.supported_languages' => array(
                'en_US' => 'English - United States',
                'ja' => 'Japanese',
            ),
            'mautic.install_source' => 'Mautic',
            'mautic.paths' => array(
                'themes' => 'themes',
                'assets' => 'media',
                'asset_prefix' => '',
                'plugins' => 'plugins',
                'translations' => 'translations',
                'local_config' => ($this->targetDirs[2].'/config/local.php'),
                'root' => $this->targetDirs[3],
                'app' => 'app',
                'bundles' => 'app/bundles',
                'vendor' => 'vendor',
            ),
            'mautic.parameters' => array(
                'site_url' => 'http://ma.labots.co',
                'webroot' => NULL,
                'cache_path' => $this->targetDirs[1],
                'log_path' => ($this->targetDirs[2].'/logs'),
                'image_path' => 'media/images',
                'theme' => 'Mauve',
                'db_driver' => 'pdo_mysql',
                'db_host' => 'localhost',
                'db_port' => '3306',
                'db_name' => 'autoMarket',
                'db_user' => 'root',
                'db_password' => 'labots.co',
                'db_table_prefix' => 'ma',
                'db_path' => NULL,
                'locale' => 'ja',
                'secret_key' => '4c8d4301b58c8edcdd609e87134e4f8a63be002c3a3014a22f664d3b4e706f5a',
                'trusted_hosts' => array(
                ),
                'trusted_proxies' => array(
                ),
                'rememberme_key' => 'ab90c625f80f8126309fec4fae1da7fff78001dd',
                'rememberme_lifetime' => '31536000',
                'rememberme_path' => '/',
                'rememberme_domain' => NULL,
                'default_pagelimit' => 30,
                'default_timezone' => 'Asia/Tokyo',
                'date_format_full' => 'F j, Y g:i a T',
                'date_format_short' => 'D, M d',
                'date_format_dateonly' => 'F j, Y',
                'date_format_timeonly' => 'g:i a',
                'ip_lookup_service' => 'telize',
                'ip_lookup_auth' => NULL,
                'transifex_username' => '',
                'transifex_password' => '',
                'update_stability' => 'stable',
                'cookie_path' => '/',
                'cookie_domain' => NULL,
                'cookie_secure' => false,
                'cookie_httponly' => false,
                'do_not_track_ips' => array(
                ),
                'cat_in_page_url' => false,
                'google_analytics' => NULL,
                'redirect_list_types' => array(
                    0 => 'mautic.page.form.redirecttype.permanent',
                    1 => 'mautic.page.form.redirecttype.temporary',
                ),
                'api_enabled' => false,
                'api_oauth2_access_token_lifetime' => 60,
                'api_oauth2_refresh_token_lifetime' => 14,
                'mailer_from_name' => 'labots co',
                'mailer_from_email' => 'labots.co@gmail.com',
                'mailer_return_path' => NULL,
                'mailer_transport' => 'mail',
                'mailer_host' => NULL,
                'mailer_port' => NULL,
                'mailer_user' => NULL,
                'mailer_password' => NULL,
                'mailer_encryption' => NULL,
                'mailer_auth_mode' => NULL,
                'mailer_spool_type' => 'memory',
                'mailer_spool_path' => ($this->targetDirs[2].'/spool'),
                'mailer_spool_msg_limit' => NULL,
                'mailer_spool_time_limit' => NULL,
                'mailer_spool_recover_timeout' => '900',
                'mailer_spool_clear_timeout' => '1800',
                'unsubscribe_text' => '<a href=\'|URL|\'>Unsubscribe</a> to no longer receive emails from us.',
                'webview_text' => '<a href=\'|URL|\'>Having trouble reading this email? Click here.</a>',
                'unsubscribe_message' => 'We are sorry to see you go! |EMAIL| will no longer receive emails from us. If this was by mistake, <a href=\'|URL|\'>click here to re-subscribe</a>.',
                'resubscribe_message' => '|EMAIL| has been re-subscribed. If this was by mistake, <a href=\'|URL|\'>click here to unsubscribe</a>.',
                'monitored_email' => array(
                    'general' => array(
                        'address' => '',
                        'host' => '',
                        'port' => '993',
                        'encryption' => '/ssl',
                        'user' => '',
                        'password' => '',
                    ),
                    'EmailBundle_bounces' => array(
                        'address' => '',
                        'host' => '',
                        'port' => '993',
                        'encryption' => '/ssl',
                        'user' => '',
                        'password' => '',
                        'override_settings' => '',
                        'folder' => '',
                        'ssl' => '1',
                    ),
                    'EmailBundle_unsubscribes' => array(
                        'address' => '',
                        'host' => '',
                        'port' => '993',
                        'encryption' => '/ssl',
                        'user' => '',
                        'password' => '',
                        'override_settings' => '',
                        'folder' => '',
                        'ssl' => '1',
                    ),
                ),
                'webhook_start' => 0,
                'webhook_limit' => 1000,
                'webhook_log_max' => 10,
                'queue_mode' => 'immediate_process',
                'upload_dir' => ($this->targetDirs[2].'/../media/files'),
                'max_size' => '6',
                'allowed_extensions' => array(
                    0 => 'csv',
                    1 => 'doc',
                    2 => 'docx',
                    3 => 'epub',
                    4 => 'gif',
                    5 => 'jpg',
                    6 => 'jpeg',
                    7 => 'mpg',
                    8 => 'mpeg',
                    9 => 'mp3',
                    10 => 'odt',
                    11 => 'odp',
                    12 => 'ods',
                    13 => 'pdf',
                    14 => 'png',
                    15 => 'ppt',
                    16 => 'pptx',
                    17 => 'tif',
                    18 => 'tiff',
                    19 => 'txt',
                    20 => 'xls',
                    21 => 'xlsx',
                    22 => 'wav',
                ),
                'supported_languages' => array(
                    'en_US' => 'English - United States',
                    'ja' => 'Japanese',
                ),
                'install_source' => 'Mautic',
                'paths' => array(
                    'themes' => 'themes',
                    'assets' => 'media',
                    'asset_prefix' => '',
                    'plugins' => 'plugins',
                    'translations' => 'translations',
                    'local_config' => ($this->targetDirs[2].'/config/local.php'),
                    'root' => $this->targetDirs[3],
                    'app' => 'app',
                    'bundles' => 'app/bundles',
                    'vendor' => 'vendor',
                ),
            ),
            'router.request_context.host' => 'ma.labots.co',
            'router.request_context.scheme' => 'http',
            'router.request_context.base_url' => '',
            'jms_serializer.camel_case_naming_strategy.class' => 'JMS\\Serializer\\Naming\\IdenticalPropertyNamingStrategy',
            'mautic.security.restrictedconfigfields' => array(
                0 => 'db_driver',
                1 => 'db_host',
                2 => 'db_table_prefix',
                3 => 'db_name',
                4 => 'db_user',
                5 => 'db_password',
                6 => 'db_path',
                7 => 'db_port',
                8 => 'secret_key',
                9 => 'transifex_username',
                10 => 'transifex_password',
            ),
            'controller_resolver.class' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ControllerResolver',
            'controller_name_converter.class' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ControllerNameParser',
            'response_listener.class' => 'Symfony\\Component\\HttpKernel\\EventListener\\ResponseListener',
            'streamed_response_listener.class' => 'Symfony\\Component\\HttpKernel\\EventListener\\StreamedResponseListener',
            'locale_listener.class' => 'Symfony\\Component\\HttpKernel\\EventListener\\LocaleListener',
            'event_dispatcher.class' => 'Symfony\\Component\\EventDispatcher\\ContainerAwareEventDispatcher',
            'http_kernel.class' => 'Symfony\\Component\\HttpKernel\\DependencyInjection\\ContainerAwareHttpKernel',
            'filesystem.class' => 'Symfony\\Component\\Filesystem\\Filesystem',
            'cache_warmer.class' => 'Symfony\\Component\\HttpKernel\\CacheWarmer\\CacheWarmerAggregate',
            'cache_clearer.class' => 'Symfony\\Component\\HttpKernel\\CacheClearer\\ChainCacheClearer',
            'file_locator.class' => 'Symfony\\Component\\HttpKernel\\Config\\FileLocator',
            'uri_signer.class' => 'Symfony\\Component\\HttpKernel\\UriSigner',
            'request_stack.class' => 'Symfony\\Component\\HttpFoundation\\RequestStack',
            'fragment.handler.class' => 'Symfony\\Component\\HttpKernel\\Fragment\\FragmentHandler',
            'fragment.renderer.inline.class' => 'Symfony\\Component\\HttpKernel\\Fragment\\InlineFragmentRenderer',
            'fragment.renderer.hinclude.class' => 'Symfony\\Bundle\\FrameworkBundle\\Fragment\\ContainerAwareHIncludeFragmentRenderer',
            'fragment.renderer.hinclude.global_template' => NULL,
            'fragment.renderer.esi.class' => 'Symfony\\Component\\HttpKernel\\Fragment\\EsiFragmentRenderer',
            'fragment.path' => '/_fragment',
            'translator.class' => 'Mautic\\CoreBundle\\Translation\\Translator',
            'translator.identity.class' => 'Symfony\\Component\\Translation\\IdentityTranslator',
            'translator.selector.class' => 'Symfony\\Component\\Translation\\MessageSelector',
            'translation.loader.php.class' => 'Symfony\\Component\\Translation\\Loader\\PhpFileLoader',
            'translation.loader.yml.class' => 'Symfony\\Component\\Translation\\Loader\\YamlFileLoader',
            'translation.loader.xliff.class' => 'Symfony\\Component\\Translation\\Loader\\XliffFileLoader',
            'translation.loader.po.class' => 'Symfony\\Component\\Translation\\Loader\\PoFileLoader',
            'translation.loader.mo.class' => 'Symfony\\Component\\Translation\\Loader\\MoFileLoader',
            'translation.loader.qt.class' => 'Symfony\\Component\\Translation\\Loader\\QtFileLoader',
            'translation.loader.csv.class' => 'Symfony\\Component\\Translation\\Loader\\CsvFileLoader',
            'translation.loader.res.class' => 'Symfony\\Component\\Translation\\Loader\\IcuResFileLoader',
            'translation.loader.dat.class' => 'Symfony\\Component\\Translation\\Loader\\IcuDatFileLoader',
            'translation.loader.ini.class' => 'Symfony\\Component\\Translation\\Loader\\IniFileLoader',
            'translation.loader.json.class' => 'Symfony\\Component\\Translation\\Loader\\JsonFileLoader',
            'translation.dumper.php.class' => 'Symfony\\Component\\Translation\\Dumper\\PhpFileDumper',
            'translation.dumper.xliff.class' => 'Symfony\\Component\\Translation\\Dumper\\XliffFileDumper',
            'translation.dumper.po.class' => 'Symfony\\Component\\Translation\\Dumper\\PoFileDumper',
            'translation.dumper.mo.class' => 'Symfony\\Component\\Translation\\Dumper\\MoFileDumper',
            'translation.dumper.yml.class' => 'Symfony\\Component\\Translation\\Dumper\\YamlFileDumper',
            'translation.dumper.qt.class' => 'Symfony\\Component\\Translation\\Dumper\\QtFileDumper',
            'translation.dumper.csv.class' => 'Symfony\\Component\\Translation\\Dumper\\CsvFileDumper',
            'translation.dumper.ini.class' => 'Symfony\\Component\\Translation\\Dumper\\IniFileDumper',
            'translation.dumper.json.class' => 'Symfony\\Component\\Translation\\Dumper\\JsonFileDumper',
            'translation.dumper.res.class' => 'Symfony\\Component\\Translation\\Dumper\\IcuResFileDumper',
            'translation.extractor.php.class' => 'Symfony\\Bundle\\FrameworkBundle\\Translation\\PhpExtractor',
            'translation.loader.class' => 'Symfony\\Bundle\\FrameworkBundle\\Translation\\TranslationLoader',
            'translation.extractor.class' => 'Symfony\\Component\\Translation\\Extractor\\ChainExtractor',
            'translation.writer.class' => 'Symfony\\Component\\Translation\\Writer\\TranslationWriter',
            'property_accessor.class' => 'Symfony\\Component\\PropertyAccess\\PropertyAccessor',
            'debug.errors_logger_listener.class' => 'Symfony\\Component\\HttpKernel\\EventListener\\ErrorsLoggerListener',
            'kernel.secret' => '4c8d4301b58c8edcdd609e87134e4f8a63be002c3a3014a22f664d3b4e706f5a',
            'kernel.http_method_override' => true,
            'kernel.trusted_hosts' => array(
            ),
            'kernel.trusted_proxies' => array(
            ),
            'kernel.default_locale' => 'ja',
            'session.class' => 'Symfony\\Component\\HttpFoundation\\Session\\Session',
            'session.flashbag.class' => 'Symfony\\Component\\HttpFoundation\\Session\\Flash\\FlashBag',
            'session.attribute_bag.class' => 'Symfony\\Component\\HttpFoundation\\Session\\Attribute\\AttributeBag',
            'session.storage.metadata_bag.class' => 'Symfony\\Component\\HttpFoundation\\Session\\Storage\\MetadataBag',
            'session.metadata.storage_key' => '_sf2_meta',
            'session.storage.native.class' => 'Symfony\\Component\\HttpFoundation\\Session\\Storage\\NativeSessionStorage',
            'session.storage.php_bridge.class' => 'Symfony\\Component\\HttpFoundation\\Session\\Storage\\PhpBridgeSessionStorage',
            'session.storage.mock_file.class' => 'Symfony\\Component\\HttpFoundation\\Session\\Storage\\MockFileSessionStorage',
            'session.handler.native_file.class' => 'Symfony\\Component\\HttpFoundation\\Session\\Storage\\Handler\\NativeFileSessionHandler',
            'session.handler.write_check.class' => 'Symfony\\Component\\HttpFoundation\\Session\\Storage\\Handler\\WriteCheckSessionHandler',
            'session_listener.class' => 'Symfony\\Bundle\\FrameworkBundle\\EventListener\\SessionListener',
            'session.storage.options' => array(
                'name' => 'fb383c994053a92e79536ebaf21cb996',
                'gc_probability' => 1,
            ),
            'session.save_path' => (__DIR__.'/sessions'),
            'session.metadata.update_threshold' => '0',
            'security.secure_random.class' => 'Symfony\\Component\\Security\\Core\\Util\\SecureRandom',
            'form.resolved_type_factory.class' => 'Symfony\\Component\\Form\\ResolvedFormTypeFactory',
            'form.registry.class' => 'Symfony\\Component\\Form\\FormRegistry',
            'form.factory.class' => 'Symfony\\Component\\Form\\FormFactory',
            'form.extension.class' => 'Symfony\\Component\\Form\\Extension\\DependencyInjection\\DependencyInjectionExtension',
            'form.type_guesser.validator.class' => 'Symfony\\Component\\Form\\Extension\\Validator\\ValidatorTypeGuesser',
            'form.type_extension.form.request_handler.class' => 'Symfony\\Component\\Form\\Extension\\HttpFoundation\\HttpFoundationRequestHandler',
            'form.type_extension.csrf.enabled' => true,
            'form.type_extension.csrf.field_name' => '_token',
            'security.csrf.token_generator.class' => 'Symfony\\Component\\Security\\Csrf\\TokenGenerator\\UriSafeTokenGenerator',
            'security.csrf.token_storage.class' => 'Symfony\\Component\\Security\\Csrf\\TokenStorage\\SessionTokenStorage',
            'security.csrf.token_manager.class' => 'Symfony\\Component\\Security\\Csrf\\CsrfTokenManager',
            'templating.engine.delegating.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\DelegatingEngine',
            'templating.name_parser.class' => 'Mautic\\CoreBundle\\Templating\\TemplateNameParser',
            'templating.filename_parser.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\TemplateFilenameParser',
            'templating.cache_warmer.template_paths.class' => 'Symfony\\Bundle\\FrameworkBundle\\CacheWarmer\\TemplatePathsCacheWarmer',
            'templating.locator.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Loader\\TemplateLocator',
            'templating.loader.filesystem.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Loader\\FilesystemLoader',
            'templating.loader.cache.class' => 'Symfony\\Component\\Templating\\Loader\\CacheLoader',
            'templating.loader.chain.class' => 'Symfony\\Component\\Templating\\Loader\\ChainLoader',
            'templating.finder.class' => 'Symfony\\Bundle\\FrameworkBundle\\CacheWarmer\\TemplateFinder',
            'templating.engine.php.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\PhpEngine',
            'templating.helper.slots.class' => 'Mautic\\CoreBundle\\Templating\\Helper\\SlotsHelper',
            'templating.helper.assets.class' => 'Mautic\\CoreBundle\\Templating\\Helper\\AssetsHelper',
            'templating.helper.actions.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\ActionsHelper',
            'templating.helper.router.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\RouterHelper',
            'templating.helper.request.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\RequestHelper',
            'templating.helper.session.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\SessionHelper',
            'templating.helper.code.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\CodeHelper',
            'templating.helper.translator.class' => 'Mautic\\CoreBundle\\Templating\\Helper\\TranslatorHelper',
            'templating.helper.form.class' => 'Mautic\\CoreBundle\\Templating\\Helper\\FormHelper',
            'templating.helper.stopwatch.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\StopwatchHelper',
            'templating.form.engine.class' => 'Symfony\\Component\\Form\\Extension\\Templating\\TemplatingRendererEngine',
            'templating.form.renderer.class' => 'Symfony\\Component\\Form\\FormRenderer',
            'templating.globals.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\GlobalVariables',
            'templating.asset.path_package.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Asset\\PathPackage',
            'templating.asset.url_package.class' => 'Symfony\\Component\\Templating\\Asset\\UrlPackage',
            'templating.asset.package_factory.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Asset\\PackageFactory',
            'templating.helper.code.file_link_format' => NULL,
            'templating.helper.form.resources' => array(
                0 => 'FrameworkBundle:Form',
                1 => 'MauticCoreBundle:FormTheme\\Custom',
            ),
            'templating.loader.cache.path' => NULL,
            'templating.engines' => array(
                0 => 'php',
            ),
            'validator.class' => 'Symfony\\Component\\Validator\\ValidatorInterface',
            'validator.builder.class' => 'Symfony\\Component\\Validator\\ValidatorBuilderInterface',
            'validator.builder.factory.class' => 'Symfony\\Component\\Validator\\Validation',
            'validator.mapping.cache.apc.class' => 'Symfony\\Component\\Validator\\Mapping\\Cache\\ApcCache',
            'validator.mapping.cache.prefix' => '',
            'validator.validator_factory.class' => 'Symfony\\Bundle\\FrameworkBundle\\Validator\\ConstraintValidatorFactory',
            'validator.expression.class' => 'Symfony\\Component\\Validator\\Constraints\\ExpressionValidator',
            'validator.email.class' => 'Symfony\\Component\\Validator\\Constraints\\EmailValidator',
            'validator.translation_domain' => 'validators',
            'validator.api' => 3,
            'fragment.listener.class' => 'Symfony\\Component\\HttpKernel\\EventListener\\FragmentListener',
            'data_collector.templates' => array(
            ),
            'router.class' => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\Router',
            'router.request_context.class' => 'Symfony\\Component\\Routing\\RequestContext',
            'routing.loader.class' => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\DelegatingLoader',
            'routing.resolver.class' => 'Symfony\\Component\\Config\\Loader\\LoaderResolver',
            'routing.loader.xml.class' => 'Symfony\\Component\\Routing\\Loader\\XmlFileLoader',
            'routing.loader.yml.class' => 'Symfony\\Component\\Routing\\Loader\\YamlFileLoader',
            'routing.loader.php.class' => 'Symfony\\Component\\Routing\\Loader\\PhpFileLoader',
            'router.options.generator_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'router.options.generator_base_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'router.options.generator_dumper_class' => 'Symfony\\Component\\Routing\\Generator\\Dumper\\PhpGeneratorDumper',
            'router.options.matcher_class' => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\RedirectableUrlMatcher',
            'router.options.matcher_base_class' => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\RedirectableUrlMatcher',
            'router.options.matcher_dumper_class' => 'Symfony\\Component\\Routing\\Matcher\\Dumper\\PhpMatcherDumper',
            'router.cache_warmer.class' => 'Symfony\\Bundle\\FrameworkBundle\\CacheWarmer\\RouterCacheWarmer',
            'router.options.matcher.cache_class' => 'appProdUrlMatcher',
            'router.options.generator.cache_class' => 'appProdUrlGenerator',
            'router_listener.class' => 'Symfony\\Component\\HttpKernel\\EventListener\\RouterListener',
            'router.resource' => ($this->targetDirs[2].'/config/routing.php'),
            'router.cache_class_prefix' => 'appProd',
            'request_listener.http_port' => 80,
            'request_listener.https_port' => 443,
            'annotations.reader.class' => 'Doctrine\\Common\\Annotations\\AnnotationReader',
            'annotations.cached_reader.class' => 'Doctrine\\Common\\Annotations\\CachedReader',
            'annotations.file_cache_reader.class' => 'Doctrine\\Common\\Annotations\\FileCacheReader',
            'security.context.class' => 'Symfony\\Component\\Security\\Core\\SecurityContext',
            'security.user_checker.class' => 'Symfony\\Component\\Security\\Core\\User\\UserChecker',
            'security.encoder_factory.generic.class' => 'Symfony\\Component\\Security\\Core\\Encoder\\EncoderFactory',
            'security.encoder.digest.class' => 'Symfony\\Component\\Security\\Core\\Encoder\\MessageDigestPasswordEncoder',
            'security.encoder.plain.class' => 'Symfony\\Component\\Security\\Core\\Encoder\\PlaintextPasswordEncoder',
            'security.encoder.pbkdf2.class' => 'Symfony\\Component\\Security\\Core\\Encoder\\Pbkdf2PasswordEncoder',
            'security.encoder.bcrypt.class' => 'Symfony\\Component\\Security\\Core\\Encoder\\BCryptPasswordEncoder',
            'security.user.provider.in_memory.class' => 'Symfony\\Component\\Security\\Core\\User\\InMemoryUserProvider',
            'security.user.provider.in_memory.user.class' => 'Symfony\\Component\\Security\\Core\\User\\User',
            'security.user.provider.chain.class' => 'Symfony\\Component\\Security\\Core\\User\\ChainUserProvider',
            'security.authentication.trust_resolver.class' => 'Symfony\\Component\\Security\\Core\\Authentication\\AuthenticationTrustResolver',
            'security.authentication.trust_resolver.anonymous_class' => 'Symfony\\Component\\Security\\Core\\Authentication\\Token\\AnonymousToken',
            'security.authentication.trust_resolver.rememberme_class' => 'Symfony\\Component\\Security\\Core\\Authentication\\Token\\RememberMeToken',
            'security.authentication.manager.class' => 'Symfony\\Component\\Security\\Core\\Authentication\\AuthenticationProviderManager',
            'security.authentication.session_strategy.class' => 'Symfony\\Component\\Security\\Http\\Session\\SessionAuthenticationStrategy',
            'security.access.decision_manager.class' => 'Symfony\\Component\\Security\\Core\\Authorization\\AccessDecisionManager',
            'security.access.simple_role_voter.class' => 'Symfony\\Component\\Security\\Core\\Authorization\\Voter\\RoleVoter',
            'security.access.authenticated_voter.class' => 'Symfony\\Component\\Security\\Core\\Authorization\\Voter\\AuthenticatedVoter',
            'security.access.role_hierarchy_voter.class' => 'Symfony\\Component\\Security\\Core\\Authorization\\Voter\\RoleHierarchyVoter',
            'security.access.expression_voter.class' => 'Symfony\\Component\\Security\\Core\\Authorization\\Voter\\ExpressionVoter',
            'security.firewall.class' => 'Symfony\\Component\\Security\\Http\\Firewall',
            'security.firewall.map.class' => 'Symfony\\Bundle\\SecurityBundle\\Security\\FirewallMap',
            'security.firewall.context.class' => 'Symfony\\Bundle\\SecurityBundle\\Security\\FirewallContext',
            'security.matcher.class' => 'Symfony\\Component\\HttpFoundation\\RequestMatcher',
            'security.expression_matcher.class' => 'Symfony\\Component\\HttpFoundation\\ExpressionRequestMatcher',
            'security.role_hierarchy.class' => 'Symfony\\Component\\Security\\Core\\Role\\RoleHierarchy',
            'security.http_utils.class' => 'Symfony\\Component\\Security\\Http\\HttpUtils',
            'security.validator.user_password.class' => 'Symfony\\Component\\Security\\Core\\Validator\\Constraints\\UserPasswordValidator',
            'security.expression_language.class' => 'Symfony\\Component\\Security\\Core\\Authorization\\ExpressionLanguage',
            'security.authentication.retry_entry_point.class' => 'Symfony\\Component\\Security\\Http\\EntryPoint\\RetryAuthenticationEntryPoint',
            'security.channel_listener.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\ChannelListener',
            'security.authentication.form_entry_point.class' => 'Symfony\\Component\\Security\\Http\\EntryPoint\\FormAuthenticationEntryPoint',
            'security.authentication.listener.form.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\UsernamePasswordFormAuthenticationListener',
            'security.authentication.listener.simple_form.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\SimpleFormAuthenticationListener',
            'security.authentication.listener.simple_preauth.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\SimplePreAuthenticationListener',
            'security.authentication.listener.basic.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\BasicAuthenticationListener',
            'security.authentication.basic_entry_point.class' => 'Symfony\\Component\\Security\\Http\\EntryPoint\\BasicAuthenticationEntryPoint',
            'security.authentication.listener.digest.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\DigestAuthenticationListener',
            'security.authentication.digest_entry_point.class' => 'Symfony\\Component\\Security\\Http\\EntryPoint\\DigestAuthenticationEntryPoint',
            'security.authentication.listener.x509.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\X509AuthenticationListener',
            'security.authentication.listener.anonymous.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\AnonymousAuthenticationListener',
            'security.authentication.switchuser_listener.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\SwitchUserListener',
            'security.logout_listener.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\LogoutListener',
            'security.logout.handler.session.class' => 'Symfony\\Component\\Security\\Http\\Logout\\SessionLogoutHandler',
            'security.logout.handler.cookie_clearing.class' => 'Symfony\\Component\\Security\\Http\\Logout\\CookieClearingLogoutHandler',
            'security.logout.success_handler.class' => 'Symfony\\Component\\Security\\Http\\Logout\\DefaultLogoutSuccessHandler',
            'security.access_listener.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\AccessListener',
            'security.access_map.class' => 'Symfony\\Component\\Security\\Http\\AccessMap',
            'security.exception_listener.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\ExceptionListener',
            'security.context_listener.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\ContextListener',
            'security.authentication.provider.dao.class' => 'Symfony\\Component\\Security\\Core\\Authentication\\Provider\\DaoAuthenticationProvider',
            'security.authentication.provider.simple.class' => 'Symfony\\Component\\Security\\Core\\Authentication\\Provider\\SimpleAuthenticationProvider',
            'security.authentication.provider.pre_authenticated.class' => 'Symfony\\Component\\Security\\Core\\Authentication\\Provider\\PreAuthenticatedAuthenticationProvider',
            'security.authentication.provider.anonymous.class' => 'Symfony\\Component\\Security\\Core\\Authentication\\Provider\\AnonymousAuthenticationProvider',
            'security.authentication.success_handler.class' => 'Symfony\\Component\\Security\\Http\\Authentication\\DefaultAuthenticationSuccessHandler',
            'security.authentication.failure_handler.class' => 'Symfony\\Component\\Security\\Http\\Authentication\\DefaultAuthenticationFailureHandler',
            'security.authentication.simple_success_failure_handler.class' => 'Symfony\\Component\\Security\\Http\\Authentication\\SimpleAuthenticationHandler',
            'security.authentication.provider.rememberme.class' => 'Symfony\\Component\\Security\\Core\\Authentication\\Provider\\RememberMeAuthenticationProvider',
            'security.authentication.listener.rememberme.class' => 'Symfony\\Component\\Security\\Http\\Firewall\\RememberMeListener',
            'security.rememberme.token.provider.in_memory.class' => 'Symfony\\Component\\Security\\Core\\Authentication\\RememberMe\\InMemoryTokenProvider',
            'security.authentication.rememberme.services.persistent.class' => 'Symfony\\Component\\Security\\Http\\RememberMe\\PersistentTokenBasedRememberMeServices',
            'security.authentication.rememberme.services.simplehash.class' => 'Symfony\\Component\\Security\\Http\\RememberMe\\TokenBasedRememberMeServices',
            'security.rememberme.response_listener.class' => 'Symfony\\Component\\Security\\Http\\RememberMe\\ResponseListener',
            'templating.helper.logout_url.class' => 'Symfony\\Bundle\\SecurityBundle\\Templating\\Helper\\LogoutUrlHelper',
            'templating.helper.security.class' => 'Symfony\\Bundle\\SecurityBundle\\Templating\\Helper\\SecurityHelper',
            'twig.extension.logout_url.class' => 'Symfony\\Bundle\\SecurityBundle\\Twig\\Extension\\LogoutUrlExtension',
            'twig.extension.security.class' => 'Symfony\\Bridge\\Twig\\Extension\\SecurityExtension',
            'data_collector.security.class' => 'Symfony\\Bundle\\SecurityBundle\\DataCollector\\SecurityDataCollector',
            'security.access.denied_url' => NULL,
            'security.authentication.manager.erase_credentials' => true,
            'security.authentication.session_strategy.strategy' => 'migrate',
            'security.access.always_authenticate_before_granting' => false,
            'security.authentication.hide_user_not_found' => true,
            'security.role_hierarchy.roles' => array(
                'ROLE_ADMIN' => array(
                    0 => 'ROLE_USER',
                ),
            ),
            'monolog.logger.class' => 'Symfony\\Bridge\\Monolog\\Logger',
            'monolog.gelf.publisher.class' => 'Gelf\\MessagePublisher',
            'monolog.gelfphp.publisher.class' => 'Gelf\\Publisher',
            'monolog.handler.stream.class' => 'Mautic\\CoreBundle\\Monolog\\Handler\\PhpHandler',
            'monolog.handler.console.class' => 'Symfony\\Bridge\\Monolog\\Handler\\ConsoleHandler',
            'monolog.handler.group.class' => 'Monolog\\Handler\\GroupHandler',
            'monolog.handler.buffer.class' => 'Monolog\\Handler\\BufferHandler',
            'monolog.handler.rotating_file.class' => 'Monolog\\Handler\\RotatingFileHandler',
            'monolog.handler.syslog.class' => 'Monolog\\Handler\\SyslogHandler',
            'monolog.handler.syslogudp.class' => 'Monolog\\Handler\\SyslogUdpHandler',
            'monolog.handler.null.class' => 'Monolog\\Handler\\NullHandler',
            'monolog.handler.test.class' => 'Monolog\\Handler\\TestHandler',
            'monolog.handler.gelf.class' => 'Monolog\\Handler\\GelfHandler',
            'monolog.handler.rollbar.class' => 'Monolog\\Handler\\RollbarHandler',
            'monolog.handler.flowdock.class' => 'Monolog\\Handler\\FlowdockHandler',
            'monolog.handler.browser_console.class' => 'Monolog\\Handler\\BrowserConsoleHandler',
            'monolog.handler.firephp.class' => 'Symfony\\Bridge\\Monolog\\Handler\\FirePHPHandler',
            'monolog.handler.chromephp.class' => 'Symfony\\Bridge\\Monolog\\Handler\\ChromePhpHandler',
            'monolog.handler.debug.class' => 'Symfony\\Bridge\\Monolog\\Handler\\DebugHandler',
            'monolog.handler.swift_mailer.class' => 'Symfony\\Bridge\\Monolog\\Handler\\SwiftMailerHandler',
            'monolog.handler.native_mailer.class' => 'Monolog\\Handler\\NativeMailerHandler',
            'monolog.handler.socket.class' => 'Monolog\\Handler\\SocketHandler',
            'monolog.handler.pushover.class' => 'Monolog\\Handler\\PushoverHandler',
            'monolog.handler.raven.class' => 'Monolog\\Handler\\RavenHandler',
            'monolog.handler.newrelic.class' => 'Monolog\\Handler\\NewRelicHandler',
            'monolog.handler.hipchat.class' => 'Monolog\\Handler\\HipChatHandler',
            'monolog.handler.slack.class' => 'Monolog\\Handler\\SlackHandler',
            'monolog.handler.cube.class' => 'Monolog\\Handler\\CubeHandler',
            'monolog.handler.amqp.class' => 'Monolog\\Handler\\AmqpHandler',
            'monolog.handler.error_log.class' => 'Monolog\\Handler\\ErrorLogHandler',
            'monolog.handler.loggly.class' => 'Monolog\\Handler\\LogglyHandler',
            'monolog.handler.logentries.class' => 'Monolog\\Handler\\LogEntriesHandler',
            'monolog.handler.whatfailuregroup.class' => 'Monolog\\Handler\\WhatFailureGroupHandler',
            'monolog.activation_strategy.not_found.class' => 'Symfony\\Bundle\\MonologBundle\\NotFoundActivationStrategy',
            'monolog.handler.fingers_crossed.class' => 'Monolog\\Handler\\FingersCrossedHandler',
            'monolog.handler.fingers_crossed.error_level_activation_strategy.class' => 'Monolog\\Handler\\FingersCrossed\\ErrorLevelActivationStrategy',
            'monolog.handler.filter.class' => 'Monolog\\Handler\\FilterHandler',
            'monolog.handler.mongo.class' => 'Monolog\\Handler\\MongoDBHandler',
            'monolog.mongo.client.class' => 'MongoClient',
            'monolog.handler.elasticsearch.class' => 'Monolog\\Handler\\ElasticSearchHandler',
            'monolog.elastica.client.class' => 'Elastica\\Client',
            'monolog.swift_mailer.handlers' => array(
            ),
            'monolog.handlers_to_channels' => array(
                'monolog.handler.mautic' => array(
                    'type' => 'inclusive',
                    'elements' => array(
                        0 => 'mautic',
                    ),
                ),
                'monolog.handler.main' => array(
                    'type' => 'exclusive',
                    'elements' => array(
                        0 => 'mautic',
                    ),
                ),
            ),
            'swiftmailer.class' => 'Swift_Mailer',
            'swiftmailer.transport.sendmail.class' => 'Swift_Transport_SendmailTransport',
            'swiftmailer.transport.mail.class' => 'Swift_Transport_MailTransport',
            'swiftmailer.transport.failover.class' => 'Swift_Transport_FailoverTransport',
            'swiftmailer.plugin.redirecting.class' => 'Swift_Plugins_RedirectingPlugin',
            'swiftmailer.plugin.impersonate.class' => 'Swift_Plugins_ImpersonatePlugin',
            'swiftmailer.plugin.messagelogger.class' => 'Swift_Plugins_MessageLogger',
            'swiftmailer.plugin.antiflood.class' => 'Swift_Plugins_AntiFloodPlugin',
            'swiftmailer.transport.smtp.class' => 'Swift_Transport_EsmtpTransport',
            'swiftmailer.plugin.blackhole.class' => 'Swift_Plugins_BlackholePlugin',
            'swiftmailer.spool.file.class' => 'Swift_FileSpool',
            'swiftmailer.spool.memory.class' => 'Swift_MemorySpool',
            'swiftmailer.email_sender.listener.class' => 'Symfony\\Bundle\\SwiftmailerBundle\\EventListener\\EmailSenderListener',
            'swiftmailer.data_collector.class' => 'Symfony\\Bundle\\SwiftmailerBundle\\DataCollector\\MessageDataCollector',
            'swiftmailer.mailer.default.transport.name' => 'mail',
            'swiftmailer.mailer.default.delivery.enabled' => true,
            'swiftmailer.mailer.default.transport.smtp.encryption' => NULL,
            'swiftmailer.mailer.default.transport.smtp.port' => 25,
            'swiftmailer.mailer.default.transport.smtp.host' => NULL,
            'swiftmailer.mailer.default.transport.smtp.username' => NULL,
            'swiftmailer.mailer.default.transport.smtp.password' => NULL,
            'swiftmailer.mailer.default.transport.smtp.auth_mode' => NULL,
            'swiftmailer.mailer.default.transport.smtp.timeout' => 30,
            'swiftmailer.mailer.default.transport.smtp.source_ip' => NULL,
            'swiftmailer.mailer.default.spool.enabled' => false,
            'swiftmailer.mailer.default.plugin.impersonate' => NULL,
            'swiftmailer.mailer.default.single_address' => NULL,
            'swiftmailer.spool.enabled' => false,
            'swiftmailer.delivery.enabled' => true,
            'swiftmailer.single_address' => NULL,
            'swiftmailer.mailers' => array(
                'default' => 'swiftmailer.mailer.default',
            ),
            'swiftmailer.default_mailer' => 'default',
            'doctrine_cache.apc.class' => 'Doctrine\\Common\\Cache\\ApcCache',
            'doctrine_cache.array.class' => 'Doctrine\\Common\\Cache\\ArrayCache',
            'doctrine_cache.file_system.class' => 'Doctrine\\Common\\Cache\\FilesystemCache',
            'doctrine_cache.php_file.class' => 'Doctrine\\Common\\Cache\\PhpFileCache',
            'doctrine_cache.mongodb.class' => 'Doctrine\\Common\\Cache\\MongoDBCache',
            'doctrine_cache.mongodb.collection.class' => 'MongoCollection',
            'doctrine_cache.mongodb.connection.class' => 'MongoClient',
            'doctrine_cache.mongodb.server' => 'localhost:27017',
            'doctrine_cache.riak.class' => 'Doctrine\\Common\\Cache\\RiakCache',
            'doctrine_cache.riak.bucket.class' => 'Riak\\Bucket',
            'doctrine_cache.riak.connection.class' => 'Riak\\Connection',
            'doctrine_cache.riak.bucket_property_list.class' => 'Riak\\BucketPropertyList',
            'doctrine_cache.riak.host' => 'localhost',
            'doctrine_cache.riak.port' => 8087,
            'doctrine_cache.memcache.class' => 'Doctrine\\Common\\Cache\\MemcacheCache',
            'doctrine_cache.memcache.connection.class' => 'Memcache',
            'doctrine_cache.memcache.host' => 'localhost',
            'doctrine_cache.memcache.port' => 11211,
            'doctrine_cache.memcached.class' => 'Doctrine\\Common\\Cache\\MemcachedCache',
            'doctrine_cache.memcached.connection.class' => 'Memcached',
            'doctrine_cache.memcached.host' => 'localhost',
            'doctrine_cache.memcached.port' => 11211,
            'doctrine_cache.redis.class' => 'Doctrine\\Common\\Cache\\RedisCache',
            'doctrine_cache.redis.connection.class' => 'Redis',
            'doctrine_cache.redis.host' => 'localhost',
            'doctrine_cache.redis.port' => 6379,
            'doctrine_cache.couchbase.class' => 'Doctrine\\Common\\Cache\\CouchbaseCache',
            'doctrine_cache.couchbase.connection.class' => 'Couchbase',
            'doctrine_cache.couchbase.hostnames' => 'localhost:8091',
            'doctrine_cache.wincache.class' => 'Doctrine\\Common\\Cache\\WinCacheCache',
            'doctrine_cache.xcache.class' => 'Doctrine\\Common\\Cache\\XcacheCache',
            'doctrine_cache.zenddata.class' => 'Doctrine\\Common\\Cache\\ZendDataCache',
            'doctrine_cache.security.acl.cache.class' => 'Doctrine\\Bundle\\DoctrineCacheBundle\\Acl\\Model\\AclCache',
            'doctrine.dbal.logger.chain.class' => 'Doctrine\\DBAL\\Logging\\LoggerChain',
            'doctrine.dbal.logger.profiling.class' => 'Doctrine\\DBAL\\Logging\\DebugStack',
            'doctrine.dbal.logger.class' => 'Symfony\\Bridge\\Doctrine\\Logger\\DbalLogger',
            'doctrine.dbal.configuration.class' => 'Doctrine\\DBAL\\Configuration',
            'doctrine.data_collector.class' => 'Doctrine\\Bundle\\DoctrineBundle\\DataCollector\\DoctrineDataCollector',
            'doctrine.dbal.connection.event_manager.class' => 'Symfony\\Bridge\\Doctrine\\ContainerAwareEventManager',
            'doctrine.dbal.connection_factory.class' => 'Doctrine\\Bundle\\DoctrineBundle\\ConnectionFactory',
            'doctrine.dbal.events.mysql_session_init.class' => 'Doctrine\\DBAL\\Event\\Listeners\\MysqlSessionInit',
            'doctrine.dbal.events.oracle_session_init.class' => 'Doctrine\\DBAL\\Event\\Listeners\\OracleSessionInit',
            'doctrine.class' => 'Doctrine\\Bundle\\DoctrineBundle\\Registry',
            'doctrine.entity_managers' => array(
                'default' => 'doctrine.orm.default_entity_manager',
            ),
            'doctrine.default_entity_manager' => 'default',
            'doctrine.dbal.connection_factory.types' => array(
                'array' => array(
                    'class' => 'Mautic\\CoreBundle\\Doctrine\\Type\\ArrayType',
                    'commented' => true,
                ),
                'datetime' => array(
                    'class' => 'Mautic\\CoreBundle\\Doctrine\\Type\\UTCDateTimeType',
                    'commented' => true,
                ),
            ),
            'doctrine.connections' => array(
                'default' => 'doctrine.dbal.default_connection',
            ),
            'doctrine.default_connection' => 'default',
            'doctrine.orm.configuration.class' => 'Doctrine\\ORM\\Configuration',
            'doctrine.orm.entity_manager.class' => 'Doctrine\\ORM\\EntityManager',
            'doctrine.orm.manager_configurator.class' => 'Doctrine\\Bundle\\DoctrineBundle\\ManagerConfigurator',
            'doctrine.orm.cache.array.class' => 'Doctrine\\Common\\Cache\\ArrayCache',
            'doctrine.orm.cache.apc.class' => 'Doctrine\\Common\\Cache\\ApcCache',
            'doctrine.orm.cache.memcache.class' => 'Doctrine\\Common\\Cache\\MemcacheCache',
            'doctrine.orm.cache.memcache_host' => 'localhost',
            'doctrine.orm.cache.memcache_port' => 11211,
            'doctrine.orm.cache.memcache_instance.class' => 'Memcache',
            'doctrine.orm.cache.memcached.class' => 'Doctrine\\Common\\Cache\\MemcachedCache',
            'doctrine.orm.cache.memcached_host' => 'localhost',
            'doctrine.orm.cache.memcached_port' => 11211,
            'doctrine.orm.cache.memcached_instance.class' => 'Memcached',
            'doctrine.orm.cache.redis.class' => 'Doctrine\\Common\\Cache\\RedisCache',
            'doctrine.orm.cache.redis_host' => 'localhost',
            'doctrine.orm.cache.redis_port' => 6379,
            'doctrine.orm.cache.redis_instance.class' => 'Redis',
            'doctrine.orm.cache.xcache.class' => 'Doctrine\\Common\\Cache\\XcacheCache',
            'doctrine.orm.cache.wincache.class' => 'Doctrine\\Common\\Cache\\WinCacheCache',
            'doctrine.orm.cache.zenddata.class' => 'Doctrine\\Common\\Cache\\ZendDataCache',
            'doctrine.orm.metadata.driver_chain.class' => 'Doctrine\\Common\\Persistence\\Mapping\\Driver\\MappingDriverChain',
            'doctrine.orm.metadata.annotation.class' => 'Doctrine\\ORM\\Mapping\\Driver\\AnnotationDriver',
            'doctrine.orm.metadata.xml.class' => 'Doctrine\\ORM\\Mapping\\Driver\\SimplifiedXmlDriver',
            'doctrine.orm.metadata.yml.class' => 'Doctrine\\ORM\\Mapping\\Driver\\SimplifiedYamlDriver',
            'doctrine.orm.metadata.php.class' => 'Doctrine\\ORM\\Mapping\\Driver\\PHPDriver',
            'doctrine.orm.metadata.staticphp.class' => 'Doctrine\\ORM\\Mapping\\Driver\\StaticPHPDriver',
            'doctrine.orm.proxy_cache_warmer.class' => 'Symfony\\Bridge\\Doctrine\\CacheWarmer\\ProxyCacheWarmer',
            'form.type_guesser.doctrine.class' => 'Symfony\\Bridge\\Doctrine\\Form\\DoctrineOrmTypeGuesser',
            'doctrine.orm.validator.unique.class' => 'Symfony\\Bridge\\Doctrine\\Validator\\Constraints\\UniqueEntityValidator',
            'doctrine.orm.validator_initializer.class' => 'Symfony\\Bridge\\Doctrine\\Validator\\DoctrineInitializer',
            'doctrine.orm.security.user.provider.class' => 'Symfony\\Bridge\\Doctrine\\Security\\User\\EntityUserProvider',
            'doctrine.orm.listeners.resolve_target_entity.class' => 'Doctrine\\ORM\\Tools\\ResolveTargetEntityListener',
            'doctrine.orm.listeners.attach_entity_listeners.class' => 'Doctrine\\ORM\\Tools\\AttachEntityListenersListener',
            'doctrine.orm.naming_strategy.default.class' => 'Doctrine\\ORM\\Mapping\\DefaultNamingStrategy',
            'doctrine.orm.naming_strategy.underscore.class' => 'Doctrine\\ORM\\Mapping\\UnderscoreNamingStrategy',
            'doctrine.orm.entity_listener_resolver.class' => 'Doctrine\\ORM\\Mapping\\DefaultEntityListenerResolver',
            'doctrine.orm.second_level_cache.default_cache_factory.class' => 'Doctrine\\ORM\\Cache\\DefaultCacheFactory',
            'doctrine.orm.second_level_cache.default_region.class' => 'Doctrine\\ORM\\Cache\\Region\\DefaultRegion',
            'doctrine.orm.second_level_cache.filelock_region.class' => 'Doctrine\\ORM\\Cache\\Region\\FileLockRegion',
            'doctrine.orm.second_level_cache.logger_chain.class' => 'Doctrine\\ORM\\Cache\\Logging\\CacheLoggerChain',
            'doctrine.orm.second_level_cache.logger_statistics.class' => 'Doctrine\\ORM\\Cache\\Logging\\StatisticsCacheLogger',
            'doctrine.orm.second_level_cache.cache_configuration.class' => 'Doctrine\\ORM\\Cache\\CacheConfiguration',
            'doctrine.orm.second_level_cache.regions_configuration.class' => 'Doctrine\\ORM\\Cache\\RegionsConfiguration',
            'doctrine.orm.auto_generate_proxy_classes' => false,
            'doctrine.orm.proxy_dir' => (__DIR__.'/doctrine/orm/Proxies'),
            'doctrine.orm.proxy_namespace' => 'Proxies',
            'doctrine_migrations.dir_name' => ($this->targetDirs[2].'/migrations'),
            'doctrine_migrations.namespace' => 'Mautic\\Migrations',
            'doctrine_migrations.table_name' => 'mamigrations',
            'doctrine_migrations.name' => 'Mautic Migrations',
            'knp_menu.factory.class' => 'Knp\\Menu\\MenuFactory',
            'knp_menu.factory_extension.routing.class' => 'Knp\\Menu\\Integration\\Symfony\\RoutingExtension',
            'knp_menu.helper.class' => 'Knp\\Menu\\Twig\\Helper',
            'knp_menu.matcher.class' => 'Knp\\Menu\\Matcher\\Matcher',
            'knp_menu.menu_provider.chain.class' => 'Knp\\Menu\\Provider\\ChainProvider',
            'knp_menu.menu_provider.container_aware.class' => 'Knp\\Bundle\\MenuBundle\\Provider\\ContainerAwareProvider',
            'knp_menu.menu_provider.builder_alias.class' => 'Knp\\Bundle\\MenuBundle\\Provider\\BuilderAliasProvider',
            'knp_menu.renderer_provider.class' => 'Knp\\Bundle\\MenuBundle\\Renderer\\ContainerAwareProvider',
            'knp_menu.renderer.list.class' => 'Knp\\Menu\\Renderer\\ListRenderer',
            'knp_menu.renderer.list.options' => array(
            ),
            'knp_menu.listener.voters.class' => 'Knp\\Bundle\\MenuBundle\\EventListener\\VoterInitializerListener',
            'knp_menu.voter.router.class' => 'Knp\\Menu\\Matcher\\Voter\\RouteVoter',
            'knp_menu.templating.helper.class' => 'Knp\\Bundle\\MenuBundle\\Templating\\Helper\\MenuHelper',
            'knp_menu.default_renderer' => 'mautic',
            'fos_oauth_server.server.class' => 'OAuth2\\OAuth2',
            'fos_oauth_server.security.authentication.provider.class' => 'FOS\\OAuthServerBundle\\Security\\Authentication\\Provider\\OAuthProvider',
            'fos_oauth_server.security.authentication.listener.class' => 'Mautic\\ApiBundle\\Security\\OAuth2\\Firewall\\OAuthListener',
            'fos_oauth_server.security.entry_point.class' => 'FOS\\OAuthServerBundle\\Security\\EntryPoint\\OAuthEntryPoint',
            'fos_oauth_server.server.options' => array(
                'access_token_lifetime' => 3600,
                'refresh_token_lifetime' => 1209600,
            ),
            'fos_oauth_server.model_manager_name' => NULL,
            'fos_oauth_server.model.client.class' => 'Mautic\\ApiBundle\\Entity\\oAuth2\\Client',
            'fos_oauth_server.model.access_token.class' => 'Mautic\\ApiBundle\\Entity\\oAuth2\\AccessToken',
            'fos_oauth_server.model.refresh_token.class' => 'Mautic\\ApiBundle\\Entity\\oAuth2\\RefreshToken',
            'fos_oauth_server.model.auth_code.class' => 'Mautic\\ApiBundle\\Entity\\oAuth2\\AuthCode',
            'fos_oauth_server.template.engine' => 'php',
            'fos_oauth_server.authorize.form.type' => 'fos_oauth_server_authorize',
            'fos_oauth_server.authorize.form.name' => 'fos_oauth_server_authorize_form',
            'fos_oauth_server.authorize.form.validation_groups' => array(
                0 => 'Authorize',
                1 => 'Default',
            ),
            'bazinga.oauth.controller.server.class' => 'Bazinga\\OAuthServerBundle\\Controller\\ServerController',
            'bazinga.oauth.controller.login.class' => 'Bazinga\\OAuthServerBundle\\Controller\\LoginController',
            'bazinga.oauth.server_service.oauth.class' => 'Bazinga\\OAuthServerBundle\\Service\\OAuthServerService',
            'bazinga.oauth.server_service.xauth.class' => 'Bazinga\\OAuthServerBundle\\Service\\XAuthServerService',
            'bazinga.oauth.signature.plaintext.class' => 'Bazinga\\OAuthServerBundle\\Service\\Signature\\OAuthPlainTextSignature',
            'bazinga.oauth.signature.hmac_sha1.class' => 'Bazinga\\OAuthServerBundle\\Service\\Signature\\OAuthHmacSha1Signature',
            'bazinga.oauth.security.authentication.provider.class' => 'Mautic\\ApiBundle\\Security\\OAuth1\\Authentication\\Provider\\OAuthProvider',
            'bazinga.oauth.security.authentication.listener.class' => 'Mautic\\ApiBundle\\Security\\OAuth1\\Firewall\\OAuthListener',
            'bazinga.oauth.event_listener.request.class' => 'Mautic\\ApiBundle\\EventListener\\OAuth1\\OAuthRequestListener',
            'bazinga.oauth.event_listener.exception.class' => 'Bazinga\\OAuthServerBundle\\EventListener\\OAuthExceptionListener',
            'bazinga.oauth.provider.consumer_provider.class' => 'Bazinga\\OAuthServerBundle\\Doctrine\\Provider\\ConsumerProvider',
            'bazinga.oauth.provider.token_provider.class' => 'Bazinga\\OAuthServerBundle\\Doctrine\\Provider\\TokenProvider',
            'bazinga.oauth.backend_type_orm' => true,
            'bazinga.oauth.model.consumer.class' => 'Mautic\\ApiBundle\\Entity\\oAuth1\\Consumer',
            'bazinga.oauth.model.request_token.class' => 'Mautic\\ApiBundle\\Entity\\oAuth1\\RequestToken',
            'bazinga.oauth.model.access_token.class' => 'Mautic\\ApiBundle\\Entity\\oAuth1\\AccessToken',
            'bazinga.oauth.model_manager_name' => NULL,
            'fos_rest.serializer.exclusion_strategy.version' => '',
            'fos_rest.serializer.exclusion_strategy.groups' => '',
            'fos_rest.view_handler.jsonp.callback_param' => '',
            'fos_rest.view.exception_wrapper_handler' => 'FOS\\RestBundle\\View\\ExceptionWrapperHandler',
            'fos_rest.view_handler.default.class' => 'FOS\\RestBundle\\View\\ViewHandler',
            'fos_rest.view_handler.jsonp.class' => 'FOS\\RestBundle\\View\\JsonpHandler',
            'fos_rest.routing.loader.controller.class' => 'FOS\\RestBundle\\Routing\\Loader\\RestRouteLoader',
            'fos_rest.routing.loader.yaml_collection.class' => 'FOS\\RestBundle\\Routing\\Loader\\RestYamlCollectionLoader',
            'fos_rest.routing.loader.xml_collection.class' => 'FOS\\RestBundle\\Routing\\Loader\\RestXmlCollectionLoader',
            'fos_rest.routing.loader.processor.class' => 'FOS\\RestBundle\\Routing\\Loader\\RestRouteProcessor',
            'fos_rest.routing.loader.reader.controller.class' => 'FOS\\RestBundle\\Routing\\Loader\\Reader\\RestControllerReader',
            'fos_rest.routing.loader.reader.action.class' => 'FOS\\RestBundle\\Routing\\Loader\\Reader\\RestActionReader',
            'fos_rest.format_negotiator.class' => 'FOS\\RestBundle\\Util\\FormatNegotiator',
            'fos_rest.inflector.class' => 'FOS\\RestBundle\\Util\\Inflector\\DoctrineInflector',
            'fos_rest.request_matcher.class' => 'Symfony\\Component\\HttpFoundation\\RequestMatcher',
            'fos_rest.request.param_fetcher.class' => 'FOS\\RestBundle\\Request\\ParamFetcher',
            'fos_rest.request.param_fetcher.reader.class' => 'FOS\\RestBundle\\Request\\ParamReader',
            'fos_rest.form.extension.csrf_disable.class' => 'FOS\\RestBundle\\Form\\Extension\\DisableCSRFExtension',
            'fos_rest.disable_csrf_role' => 'ROLE_API',
            'fos_rest.cache_dir' => (__DIR__.'/fos_rest'),
            'fos_rest.serializer.serialize_null' => false,
            'fos_rest.formats' => array(
                'json' => false,
            ),
            'fos_rest.default_engine' => 'twig',
            'fos_rest.force_redirects' => array(
                'html' => 302,
            ),
            'fos_rest.failed_validation' => 400,
            'fos_rest.empty_content' => 204,
            'fos_rest.serialize_null' => false,
            'fos_rest.routing.loader.default_format' => 'json',
            'fos_rest.routing.loader.include_format' => false,
            'fos_rest.exception.codes' => array(
            ),
            'fos_rest.exception.messages' => array(
            ),
            'fos_rest.decoder.json.class' => 'FOS\\RestBundle\\Decoder\\JsonDecoder',
            'fos_rest.decoder.jsontoform.class' => 'FOS\\RestBundle\\Decoder\\JsonToFormDecoder',
            'fos_rest.decoder.xml.class' => 'FOS\\RestBundle\\Decoder\\XmlDecoder',
            'fos_rest.decoder_provider.class' => 'FOS\\RestBundle\\Decoder\\ContainerDecoderProvider',
            'fos_rest.body_listener.class' => 'FOS\\RestBundle\\EventListener\\BodyListener',
            'fos_rest.throw_exception_on_unsupported_content_type' => false,
            'fos_rest.decoders' => array(
                'json' => 'fos_rest.decoder.json',
                'xml' => 'fos_rest.decoder.xml',
            ),
            'fos_rest.mime_types' => array(
            ),
            'fos_rest.converter.request_body.validation_errors_argument' => 'validationErrors',
            'jms_serializer.metadata.file_locator.class' => 'Metadata\\Driver\\FileLocator',
            'jms_serializer.metadata.annotation_driver.class' => 'Mautic\\ApiBundle\\Serializer\\Driver\\AnnotationDriver',
            'jms_serializer.metadata.chain_driver.class' => 'Metadata\\Driver\\DriverChain',
            'jms_serializer.metadata.yaml_driver.class' => 'JMS\\Serializer\\Metadata\\Driver\\YamlDriver',
            'jms_serializer.metadata.xml_driver.class' => 'JMS\\Serializer\\Metadata\\Driver\\XmlDriver',
            'jms_serializer.metadata.php_driver.class' => 'Mautic\\ApiBundle\\Serializer\\Driver\\ApiMetadataDriver',
            'jms_serializer.metadata.doctrine_type_driver.class' => 'JMS\\Serializer\\Metadata\\Driver\\DoctrineTypeDriver',
            'jms_serializer.metadata.doctrine_phpcr_type_driver.class' => 'JMS\\Serializer\\Metadata\\Driver\\DoctrinePHPCRTypeDriver',
            'jms_serializer.metadata.lazy_loading_driver.class' => 'Metadata\\Driver\\LazyLoadingDriver',
            'jms_serializer.metadata.metadata_factory.class' => 'Metadata\\MetadataFactory',
            'jms_serializer.metadata.cache.file_cache.class' => 'Metadata\\Cache\\FileCache',
            'jms_serializer.event_dispatcher.class' => 'JMS\\Serializer\\EventDispatcher\\LazyEventDispatcher',
            'jms_serializer.serialized_name_annotation_strategy.class' => 'JMS\\Serializer\\Naming\\SerializedNameAnnotationStrategy',
            'jms_serializer.cache_naming_strategy.class' => 'JMS\\Serializer\\Naming\\CacheNamingStrategy',
            'jms_serializer.doctrine_object_constructor.class' => 'JMS\\Serializer\\Construction\\DoctrineObjectConstructor',
            'jms_serializer.unserialize_object_constructor.class' => 'JMS\\Serializer\\Construction\\UnserializeObjectConstructor',
            'jms_serializer.version_exclusion_strategy.class' => 'JMS\\Serializer\\Exclusion\\VersionExclusionStrategy',
            'jms_serializer.serializer.class' => 'JMS\\Serializer\\Serializer',
            'jms_serializer.twig_extension.class' => 'JMS\\Serializer\\Twig\\SerializerExtension',
            'jms_serializer.templating.helper.class' => 'JMS\\SerializerBundle\\Templating\\SerializerHelper',
            'jms_serializer.json_serialization_visitor.class' => 'JMS\\Serializer\\JsonSerializationVisitor',
            'jms_serializer.json_serialization_visitor.options' => 0,
            'jms_serializer.json_deserialization_visitor.class' => 'JMS\\Serializer\\JsonDeserializationVisitor',
            'jms_serializer.xml_serialization_visitor.class' => 'JMS\\Serializer\\XmlSerializationVisitor',
            'jms_serializer.xml_deserialization_visitor.class' => 'JMS\\Serializer\\XmlDeserializationVisitor',
            'jms_serializer.xml_deserialization_visitor.doctype_whitelist' => array(
            ),
            'jms_serializer.yaml_serialization_visitor.class' => 'JMS\\Serializer\\YamlSerializationVisitor',
            'jms_serializer.handler_registry.class' => 'JMS\\Serializer\\Handler\\LazyHandlerRegistry',
            'jms_serializer.datetime_handler.class' => 'JMS\\Serializer\\Handler\\DateHandler',
            'jms_serializer.array_collection_handler.class' => 'JMS\\Serializer\\Handler\\ArrayCollectionHandler',
            'jms_serializer.php_collection_handler.class' => 'JMS\\Serializer\\Handler\\PhpCollectionHandler',
            'jms_serializer.form_error_handler.class' => 'JMS\\Serializer\\Handler\\FormErrorHandler',
            'jms_serializer.constraint_violation_handler.class' => 'JMS\\Serializer\\Handler\\ConstraintViolationHandler',
            'jms_serializer.doctrine_proxy_subscriber.class' => 'JMS\\Serializer\\EventDispatcher\\Subscriber\\DoctrineProxySubscriber',
            'jms_serializer.stopwatch_subscriber.class' => 'JMS\\SerializerBundle\\Serializer\\StopwatchEventSubscriber',
            'jms_serializer.infer_types_from_doctrine_metadata' => true,
            'oneup_uploader.chunks.manager.class' => 'Oneup\\UploaderBundle\\Uploader\\Chunk\\ChunkManager',
            'oneup_uploader.chunks_storage.gaufrette.class' => 'Oneup\\UploaderBundle\\Uploader\\Chunk\\Storage\\GaufretteStorage',
            'oneup_uploader.chunks_storage.filesystem.class' => 'Oneup\\UploaderBundle\\Uploader\\Chunk\\Storage\\FilesystemStorage',
            'oneup_uploader.namer.uniqid.class' => 'Oneup\\UploaderBundle\\Uploader\\Naming\\UniqidNamer',
            'oneup_uploader.routing.loader.class' => 'Oneup\\UploaderBundle\\Routing\\RouteLoader',
            'oneup_uploader.storage.gaufrette.class' => 'Oneup\\UploaderBundle\\Uploader\\Storage\\GaufretteStorage',
            'oneup_uploader.storage.filesystem.class' => 'Oneup\\UploaderBundle\\Uploader\\Storage\\FilesystemStorage',
            'oneup_uploader.orphanage.class' => 'Oneup\\UploaderBundle\\Uploader\\Storage\\FilesystemOrphanageStorage',
            'oneup_uploader.orphanage.manager.class' => 'Oneup\\UploaderBundle\\Uploader\\Orphanage\\OrphanageManager',
            'oneup_uploader.controller.fineuploader.class' => 'Oneup\\UploaderBundle\\Controller\\FineUploaderController',
            'oneup_uploader.controller.blueimp.class' => 'Oneup\\UploaderBundle\\Controller\\BlueimpController',
            'oneup_uploader.controller.uploadify.class' => 'Oneup\\UploaderBundle\\Controller\\UploadifyController',
            'oneup_uploader.controller.yui3.class' => 'Oneup\\UploaderBundle\\Controller\\YUI3Controller',
            'oneup_uploader.controller.fancyupload.class' => 'Oneup\\UploaderBundle\\Controller\\FancyUploadController',
            'oneup_uploader.controller.mooupload.class' => 'Oneup\\UploaderBundle\\Controller\\MooUploadController',
            'oneup_uploader.controller.plupload.class' => 'Oneup\\UploaderBundle\\Controller\\PluploadController',
            'oneup_uploader.controller.dropzone.class' => 'Mautic\\AssetBundle\\Controller\\UploadController',
            'oneup_uploader.error_handler.noop.class' => 'Oneup\\UploaderBundle\\Uploader\\ErrorHandler\\NoopErrorHandler',
            'oneup_uploader.error_handler.blueimp.class' => 'Oneup\\UploaderBundle\\Uploader\\ErrorHandler\\BlueimpErrorHandler',
            'oneup_uploader.chunks' => array(
                'maxage' => 604800,
                'storage' => array(
                    'type' => 'filesystem',
                    'filesystem' => NULL,
                    'directory' => (__DIR__.'/uploader/chunks'),
                    'stream_wrapper' => NULL,
                    'sync_buffer_size' => '100K',
                    'prefix' => 'chunks',
                ),
                'load_distribution' => true,
            ),
            'oneup_uploader.orphanage' => array(
                'maxage' => 604800,
                'directory' => (__DIR__.'/uploader/orphanage'),
            ),
            'oneup_uploader.config.asset' => array(
                'error_handler' => 'mautic.asset.upload.error.handler',
                'frontend' => 'custom',
                'custom_frontend' => array(
                    'class' => 'Mautic\\AssetBundle\\Controller\\UploadController',
                    'name' => 'mautic',
                ),
                'storage' => array(
                    'directory' => ($this->targetDirs[2].'/../media/files'),
                    'service' => NULL,
                    'type' => 'filesystem',
                    'filesystem' => NULL,
                    'stream_wrapper' => NULL,
                    'sync_buffer_size' => '100K',
                ),
                'route_prefix' => '',
                'allowed_mimetypes' => array(
                ),
                'disallowed_mimetypes' => array(
                ),
                'max_size' => 9223372036854775807,
                'use_orphanage' => false,
                'enable_progress' => false,
                'enable_cancelation' => false,
                'namer' => 'oneup_uploader.namer.uniqid',
            ),
            'oneup_uploader.config' => array(
                'mappings' => array(
                    'asset' => array(
                        'error_handler' => 'mautic.asset.upload.error.handler',
                        'frontend' => 'custom',
                        'custom_frontend' => array(
                            'class' => 'Mautic\\AssetBundle\\Controller\\UploadController',
                            'name' => 'mautic',
                        ),
                        'storage' => array(
                            'directory' => ($this->targetDirs[2].'/../media/files'),
                            'service' => NULL,
                            'type' => 'filesystem',
                            'filesystem' => NULL,
                            'stream_wrapper' => NULL,
                            'sync_buffer_size' => '100K',
                        ),
                        'route_prefix' => '',
                        'allowed_mimetypes' => array(
                        ),
                        'disallowed_mimetypes' => array(
                        ),
                        'max_size' => 9223372036854775807,
                        'use_orphanage' => false,
                        'enable_progress' => false,
                        'enable_cancelation' => false,
                        'namer' => 'oneup_uploader.namer.uniqid',
                    ),
                ),
                'chunks' => array(
                    'maxage' => 604800,
                    'storage' => array(
                        'type' => 'filesystem',
                        'filesystem' => NULL,
                        'directory' => (__DIR__.'/uploader/chunks'),
                        'stream_wrapper' => NULL,
                        'sync_buffer_size' => '100K',
                        'prefix' => 'chunks',
                    ),
                    'load_distribution' => true,
                ),
                'orphanage' => array(
                    'maxage' => 604800,
                    'directory' => (__DIR__.'/uploader/orphanage'),
                ),
                'twig' => true,
            ),
            'oneup_uploader.controllers' => array(
                'asset' => array(
                    0 => 'oneup_uploader.controller.mautic',
                    1 => array(
                        'enable_progress' => false,
                        'enable_cancelation' => false,
                        'route_prefix' => '',
                    ),
                ),
            ),
            'oneup_uploader.maxsize' => array(
                'asset' => 8388608,
            ),
            'twig.controller.exception.class' => 'Mautic\\CoreBundle\\Controller\\ExceptionController',
            'console.command.ids' => array(
            ),
        );
    }
}
