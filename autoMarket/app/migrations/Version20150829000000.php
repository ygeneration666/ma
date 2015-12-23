<?php
/**
 * @package     Mautic
 * @copyright   2015 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\Migrations;

use Doctrine\DBAL\Migrations\SkipMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Mautic\CoreBundle\Doctrine\AbstractMauticMigration;

/**
 * Class Version20150829000000
 */
class Version20150829000000 extends AbstractMauticMigration
{
    /**
     * @param Schema $schema
     *
     * @throws SkipMigrationException
     */
    public function preUp(Schema $schema)
    {
        if ($schema->hasTable($this->prefix . 'lead_tags')) {
            throw new SkipMigrationException('Schema includes this migration');
        }
    }

    /**
     * @param Schema $schema
     */
    public function mysqlUp(Schema $schema)
    {
        $this->addSql('CREATE TABLE ' . $this->prefix . 'lead_tags_xref (lead_id INT NOT NULL, tag_id INT NOT NULL, INDEX ' . $this->generatePropertyName('lead_tags_xref', 'idx', array('lead_id')) . ' (lead_id), INDEX ' . $this->generatePropertyName('lead_tags_xref', 'idx', array('tag_id')) . ' (tag_id), PRIMARY KEY(lead_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ' . $this->prefix . 'lead_tags (id INT AUTO_INCREMENT NOT NULL, tag VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'lead_tags_xref ADD CONSTRAINT ' . $this->generatePropertyName('lead_tags_xref', 'fk', array('lead_id')) . ' FOREIGN KEY (lead_id) REFERENCES ' . $this->prefix . 'leads (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'lead_tags_xref ADD CONSTRAINT ' . $this->generatePropertyName('lead_tags_xref', 'fk', array('tag_id')) . ' FOREIGN KEY (tag_id) REFERENCES ' . $this->prefix . 'lead_tags (id)');

        $this->addSql('CREATE TABLE ' . $this->prefix . 'webhook_events (id INT AUTO_INCREMENT NOT NULL, webhook_id INT NOT NULL, event_type VARCHAR(50) NOT NULL, INDEX ' . $this->generatePropertyName('webhook_events', 'idx', array('webhook_id')) . ' (webhook_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ' . $this->prefix . 'webhook_logs (id INT AUTO_INCREMENT NOT NULL, webhook_id INT NOT NULL, status_code VARCHAR(50) NOT NULL, date_added DATETIME DEFAULT NULL, INDEX ' . $this->generatePropertyName('webhook_logs', 'idx', array('webhook_id')) . ' (webhook_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ' . $this->prefix . 'webhooks (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, is_published TINYINT(1) NOT NULL, date_added DATETIME DEFAULT NULL, created_by INT DEFAULT NULL, created_by_user VARCHAR(255) DEFAULT NULL, date_modified DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_by_user VARCHAR(255) DEFAULT NULL, checked_out DATETIME DEFAULT NULL, checked_out_by INT DEFAULT NULL, checked_out_by_user VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, webhook_url VARCHAR(255) NOT NULL, INDEX ' . $this->generatePropertyName('webhooks', 'idx', array('category_id')) . ' (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ' . $this->prefix . 'webhook_queue (id INT AUTO_INCREMENT NOT NULL, webhook_id INT NOT NULL, event_id INT NOT NULL, date_added DATETIME DEFAULT NULL, payload LONGTEXT NOT NULL, INDEX ' . $this->generatePropertyName('webhooks', 'idx', array('webhook_id')) . ' (webhook_id), INDEX ' . $this->generatePropertyName('webhook_queue', 'idx', array('event_id')) . ' (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'webhook_events ADD CONSTRAINT ' . $this->generatePropertyName('webhook_events', 'fk', array('webhook_id')) . ' FOREIGN KEY (webhook_id) REFERENCES ' . $this->prefix . 'webhooks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'webhook_logs ADD CONSTRAINT ' . $this->generatePropertyName('webhook_logs', 'fk', array('webhook_id')) . ' FOREIGN KEY (webhook_id) REFERENCES ' . $this->prefix . 'webhooks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'webhooks ADD CONSTRAINT ' . $this->generatePropertyName('webhooks', 'fk', array('category_id')) . ' FOREIGN KEY (category_id) REFERENCES ' . $this->prefix . 'categories (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'webhook_queue ADD CONSTRAINT ' . $this->generatePropertyName('webhook_queue', 'fk', array('webhook_id')) . ' FOREIGN KEY (webhook_id) REFERENCES ' . $this->prefix . 'webhooks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'webhook_queue ADD CONSTRAINT ' . $this->generatePropertyName('webhook_queue', 'fk', array('event_id')) . ' FOREIGN KEY (event_id) REFERENCES ' . $this->prefix . 'webhook_events (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE ' . $this->prefix . 'leads CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE country country VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'email_donotemail ADD manual TINYINT(1) DEFAULT NULL');

        $this->addSql('CREATE INDEX ' . $this->prefix . 'oauth1_access_token_search ON ' . $this->prefix . 'oauth1_access_tokens (token)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'consumer_search ON ' . $this->prefix . 'oauth1_consumers (consumer_key)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'oauth1_request_token_search ON ' . $this->prefix . 'oauth1_request_tokens (token)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'client_id_search ON ' . $this->prefix . 'oauth2_clients (random_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'asset_alias_search ON ' . $this->prefix . 'assets (alias)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'download_tracking_search ON ' . $this->prefix . 'asset_downloads (tracking_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'download_source_search ON ' . $this->prefix . 'asset_downloads (source, source_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'campaign_event_type_search ON ' . $this->prefix . 'campaign_events (type, event_type)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'event_upcoming_search ON ' . $this->prefix . 'campaign_lead_event_log (is_scheduled)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'category_alias_search ON ' . $this->prefix . 'categories (alias)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'object_search ON ' . $this->prefix . 'audit_log (object, object_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'timeline_search ON ' . $this->prefix . 'audit_log (bundle, object, action, object_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'ip_search ON ' . $this->prefix . 'ip_addresses (ip_address)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'dnc_search ON ' . $this->prefix . 'email_donotemail (address)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'stat_email_search ON ' . $this->prefix . 'email_stats (email_id, lead_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'stat_email_failed_search ON ' . $this->prefix . 'email_stats (is_failed)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'stat_email_read_search ON ' . $this->prefix . 'email_stats (is_read)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'stat_email_hash_search ON ' . $this->prefix . 'email_stats (tracking_hash)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'stat_email_source_search ON ' . $this->prefix . 'email_stats (source, source_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'form_action_type_search ON ' . $this->prefix . 'form_actions (type)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'form_field_type_search ON ' . $this->prefix . 'form_fields (type)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'form_submission_tracking_search ON ' . $this->prefix . 'form_submissions (tracking_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'lead_field_email_search ON ' . $this->prefix . 'leads (email)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'lead_field_country_search ON ' . $this->prefix . 'leads (country)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'page_hit_tracking_search ON ' . $this->prefix . 'page_hits (tracking_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'page_hit_code_search ON ' . $this->prefix . 'page_hits (code)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'page_hit_source_search ON ' . $this->prefix . 'page_hits (source, source_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'page_alias_search ON ' . $this->prefix . 'pages (alias)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'point_type_search ON ' . $this->prefix . 'points (type)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'trigger_type_search ON ' . $this->prefix . 'point_trigger_events (type)');
    }

    /**
     * @param Schema $schema
     */
    public function postgresqlUp(Schema $schema)
    {
        $this->addSql('CREATE SEQUENCE ' . $this->prefix . 'lead_tags_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ' . $this->prefix . 'lead_tags_xref (lead_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(lead_id, tag_id))');
        $this->addSql('CREATE INDEX ' . $this->generatePropertyName('lead_tags_xref', 'idx', array('lead_id')) . ' ON ' . $this->prefix . 'lead_tags_xref (lead_id)');
        $this->addSql('CREATE INDEX ' . $this->generatePropertyName('lead_tags_xref', 'idx', array('tag_id')) . ' ON ' . $this->prefix . 'lead_tags_xref (tag_id)');
        $this->addSql('CREATE TABLE ' . $this->prefix . 'lead_tags (id INT NOT NULL, tag VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'lead_tags_xref ADD CONSTRAINT ' . $this->generatePropertyName('lead_tags_xref', 'fk', array('lead_id')) . ' FOREIGN KEY (lead_id) REFERENCES ' . $this->prefix . 'leads (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'lead_tags_xref ADD CONSTRAINT ' . $this->generatePropertyName('lead_tags_xref', 'fk', array('tag_id')) . ' FOREIGN KEY (tag_id) REFERENCES ' . $this->prefix . 'lead_tags (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE SEQUENCE ' . $this->prefix . 'webhook_events_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ' . $this->prefix . 'webhook_logs_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ' . $this->prefix . 'webhooks_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ' . $this->prefix . 'webhook_queue_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ' . $this->prefix . 'webhook_events (id INT NOT NULL, webhook_id INT NOT NULL, event_type VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX ' . $this->generatePropertyName('webhook_events', 'idx', array('webhook_id')) . ' ON ' . $this->prefix . 'webhook_events (webhook_id)');
        $this->addSql('CREATE TABLE ' . $this->prefix . 'webhook_logs (id INT NOT NULL, webhook_id INT NOT NULL, status_code VARCHAR(50) NOT NULL, date_added TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX ' . $this->generatePropertyName('webhook_logs', 'idx', array('webhook_id')) . ' ON ' . $this->prefix . 'webhook_logs (webhook_id)');
        $this->addSql('CREATE TABLE ' . $this->prefix . 'webhooks (id INT NOT NULL, category_id INT DEFAULT NULL, is_published BOOLEAN NOT NULL, date_added TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_by INT DEFAULT NULL, created_by_user VARCHAR(255) DEFAULT NULL, date_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, modified_by INT DEFAULT NULL, modified_by_user VARCHAR(255) DEFAULT NULL, checked_out TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, checked_out_by INT DEFAULT NULL, checked_out_by_user VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, webhook_url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX ' . $this->generatePropertyName('webhooks', 'idx', array('category_id')) . ' ON ' . $this->prefix . 'webhooks (category_id)');
        $this->addSql('CREATE TABLE ' . $this->prefix . 'webhook_queue (id INT NOT NULL, webhook_id INT NOT NULL, event_id INT NOT NULL, date_added TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, payload TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX ' . $this->generatePropertyName('webhooks', 'idx', array('webhook_id')) . ' ON ' . $this->prefix . 'webhook_queue (webhook_id)');
        $this->addSql('CREATE INDEX ' . $this->generatePropertyName('webhook_queue', 'idx', array('event_id')) . ' ON ' . $this->prefix . 'webhook_queue (event_id)');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'webhook_events ADD CONSTRAINT ' . $this->generatePropertyName('webhook_events', 'fk', array('webhook_id')) . ' FOREIGN KEY (webhook_id) REFERENCES ' . $this->prefix . 'webhooks (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'webhook_logs ADD CONSTRAINT ' . $this->generatePropertyName('webhook_logs', 'fk', array('webhook_id')) . ' FOREIGN KEY (webhook_id) REFERENCES ' . $this->prefix . 'webhooks (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'webhooks ADD CONSTRAINT ' . $this->generatePropertyName('webhooks', 'fk', array('category_id')) . ' FOREIGN KEY (category_id) REFERENCES ' . $this->prefix . 'categories (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'webhook_queue ADD CONSTRAINT ' . $this->generatePropertyName('webhook_queue', 'fk', array('webhook_id')) . ' FOREIGN KEY (webhook_id) REFERENCES ' . $this->prefix . 'webhooks (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'webhook_queue ADD CONSTRAINT ' . $this->generatePropertyName('webhook_queue', 'fk', array('event_id')) . ' FOREIGN KEY (event_id) REFERENCES ' . $this->prefix . 'webhook_events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE ' . $this->prefix . 'email_donotemail ADD manual BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'leads ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE ' . $this->prefix . 'leads ALTER country TYPE VARCHAR(255)');

        $this->addSql('CREATE INDEX ' . $this->prefix . 'oauth1_access_token_search ON ' . $this->prefix . 'oauth1_access_tokens (token)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'consumer_search ON ' . $this->prefix . 'oauth1_consumers (consumer_key)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'oauth1_request_token_search ON ' . $this->prefix . 'oauth1_request_tokens (token)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'client_id_search ON ' . $this->prefix . 'oauth2_clients (random_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'asset_alias_search ON ' . $this->prefix . 'assets (alias)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'download_tracking_search ON ' . $this->prefix . 'asset_downloads (tracking_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'download_source_search ON ' . $this->prefix . 'asset_downloads (source, source_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'campaign_event_type_search ON ' . $this->prefix . 'campaign_events (type, event_type)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'event_upcoming_search ON ' . $this->prefix . 'campaign_lead_event_log (is_scheduled)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'category_alias_search ON ' . $this->prefix . 'categories (alias)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'object_search ON ' . $this->prefix . 'audit_log (object, object_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'timeline_search ON ' . $this->prefix . 'audit_log (bundle, object, action, object_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'ip_search ON ' . $this->prefix . 'ip_addresses (ip_address)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'dnc_search ON ' . $this->prefix . 'email_donotemail (address)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'stat_email_search ON ' . $this->prefix . 'email_stats (email_id, lead_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'stat_email_failed_search ON ' . $this->prefix . 'email_stats (is_failed)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'stat_email_read_search ON ' . $this->prefix . 'email_stats (is_read)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'stat_email_hash_search ON ' . $this->prefix . 'email_stats (tracking_hash)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'stat_email_source_search ON ' . $this->prefix . 'email_stats (source, source_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'form_action_type_search ON ' . $this->prefix . 'form_actions (type)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'form_field_type_search ON ' . $this->prefix . 'form_fields (type)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'form_submission_tracking_search ON ' . $this->prefix . 'form_submissions (tracking_id)');
        $this->addSql('CREATE INDEX lead_field' . $this->prefix . 'email_search ON ' . $this->prefix . 'leads (email)');
        $this->addSql('CREATE INDEX lead_field' . $this->prefix . 'country_search ON ' . $this->prefix . 'leads (country)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'page_hit_tracking_search ON ' . $this->prefix . 'page_hits (tracking_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'page_hit_code_search ON ' . $this->prefix . 'page_hits (code)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'page_hit_source_search ON ' . $this->prefix . 'page_hits (source, source_id)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'page_alias_search ON ' . $this->prefix . 'pages (alias)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'point_type_search ON ' . $this->prefix . 'points (type)');
        $this->addSql('CREATE INDEX ' . $this->prefix . 'trigger_type_search ON ' . $this->prefix . 'point_trigger_events (type)');
    }
}
