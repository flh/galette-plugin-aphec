DROP TABLE IF EXISTS galette_aphec_lists_profiles;
DROP TABLE IF EXISTS galette_aphec_lists_subscriptions;
DROP TABLE IF EXISTS galette_aphec_lists;

-- Listes de diffusion auxquelles les adhérents peuvent s'inscrire librement.
-- Ceci est destiné à regrouper toutes les listes par matière enseignée.
CREATE TABLE galette_aphec_lists (
  id_list int(10) unsigned NOT NULL auto_increment,
  sympa_name VARCHAR(50) NOT NULL,
  sympa_description text,
  authorized tinyint(1) default 0 NOT NULL,
  PRIMARY KEY (id_list),
  UNIQUE (sympa_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Cette table définit les inscriptions des adhérents aux listes de diffusion
-- choisies manuellement. Si un abonné préfère le mode automatique, il n'y a
-- aucune entrée dans la table galette_aphec_lists_subscriptions pour la liste. Si en
-- revanche il est inscrit, le choix indiqué dans "is_subscribed" a la priorité
-- sur le choix donné automatiquement par la matière.
CREATE TABLE galette_aphec_lists_subscriptions (
  id_adh int(10) unsigned NOT NULL,
  id_list int(10) unsigned NOT NULL,
  is_subscribed tinyint(1) NOT NULL,
  FOREIGN KEY (id_list) REFERENCES galette_aphec_lists (id_list) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (id_adh) REFERENCES galette_adherents (id_adh) ON DELETE CASCADE ON UPDATE CASCADE
  PRIMARY KEY (id_adh, id_list)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Cette table définit l'ensemble des listes de diffusion par défaut selon les
-- matières des adhérents.
CREATE TABLE galette_aphec_lists_profiles (
  id_list int(10) unsigned NOT NULL,
  id_profile int(11) NOT NULL,
  UNIQUE (id_list, id_profile),
  PRIMARY KEY (id_list, id_profile),
  FOREIGN KEY (id_list) REFERENCES galette_aphec_lists (id_list) ON DELETE CASCADE ON UPDATE CASCADE
  -- La table galette_field_contents_4 est créée par le champ dynamique n°4
  -- dans Galette, ce qui est hautement variable d'une installation à l'autre.
  -- Cette référence est précaire.
  -- FOREIGN KEY (id_profile) REFERENCES galette_field_contents_4 (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
