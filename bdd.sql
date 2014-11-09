INSERT INTO `fos_group` (`id`, `name`, `roles`) VALUES
(1, 'Groupe Super Admin', 'a:2:{i:0;s:10:"ROLE_ADMIN";i:1;s:16:"ROLE_SUPER_ADMIN";}'),
(2, 'Groupe Admin', 'a:1:{i:0;s:10:"ROLE_ADMIN";}'),
(3, 'Groupe User', 'a:1:{i:0;s:9:"ROLE_USER";}');

INSERT INTO `fos_user` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`, `firstname`, `lastname`, `firstconnexion`) VALUES
(1, 'admin', 'admin', 'admin@mail.com', 'admin@mail.com', 1, '8x80k677924gwooogs0kw0kkk4cos4c', 'U1DLbXoX1ZcZoyKPP1UxjAwZj8Ofkh5xieGQnDEdvgA6q5wGrnZi35llyyhSGgITjCfdjhVmL3xLKbXvK5+l/g==', '2014-10-10 18:28:55', 0, 0, NULL, NULL, NULL, 'a:0:{}', 0, NULL, '', '', 1),
(2, 'superadmin', 'superadmin', 'superadmin@mail.com', 'superadmin@mail.com', 1, '2l4lgrww8jswwsc844o4gkgsw404ocw', 'h3AHoTssUigo2FF8fOsJil/AJu4hSb3Ba4w0CZOwbG+I8pW0JjPoDb0BkLnFqMDZa/srF+3kfvGqKFPmCM3ynA==', '2014-10-15 17:49:42', 0, 0, NULL, NULL, NULL, 'a:0:{}', 0, NULL, '', '', 1),
(3, 'user', 'user', 'user@mail.com', 'user@mail.com', 1, 's9yh737zzm88o4ww0840wow0kgk4ooc', 'NhmcMteVmOZG05CCRh1g6Kupt+UKjv9uomubYBincz5f85RqRJ/2mXVOO0NPAUspvir3/dggYFja4COcGyNWZA==', '2014-10-10 14:14:09', 0, 0, NULL, NULL, NULL, 'a:0:{}', 0, NULL, '', '', 1);

INSERT INTO `fos_user_group` (`user_id`, `group_id`) VALUES
(1, 2),
(2, 1),
(3, 3);

INSERT INTO `language` (`id`, `created_at`, `updated_at`, `archived_at`, `enabled`, `position`, `iso_code`) VALUES
(1, '2014-10-16 15:35:03', '2014-10-16 15:35:03', NULL, 1, 0, 'fr'),
(2, '2014-10-16 15:35:19', '2014-10-16 15:35:19', NULL, 1, 1, 'en');
