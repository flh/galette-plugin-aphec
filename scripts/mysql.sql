DROP TABLE IF EXISTS aphec_lists;
CREATE TABLE aphec_lists (
  id_list int(10) unsigned NOT NULL auto_increment,
  sympa_name VARCHAR(50) NOT NULL,
  sympa_description text,
  PRIMARY_KEY (id_list),
  UNIQUE (sympa_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS aphec_lists_subscriptions;
CREATE TABLE aphec_lists_subscriptions (
  id_adh int(10) unsigned NOT NULL,
  id_list int(10) unsigned NOT NULL,
  is_automatic tinyint(1) NOT NULL default 1, 
  FOREIGN KEY (id_list) REFERENCES aphec_lists (id_list) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (id_adh) REFERENCES galette_adherents (id_adh) ON DELETE CASCADE ON UPDATE CASCADE,
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
