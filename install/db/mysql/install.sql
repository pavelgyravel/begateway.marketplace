create table if not exists b_bemarketplace_applications (
	ID int(18) not null auto_increment,
  SITE_ID char(2) not null,
  CLIENT_ID varchar(255) not null,
  CLIENT_SECRET varchar(255) not null,
  HOST varchar(255) not null,

	PRIMARY KEY (ID)
);

INSERT INTO b_bemarketplace_applications (SITE_ID, CLIENT_ID, CLIENT_SECRET, HOST) values ("s1", "partner_client_id", "partner_client_secret", "http://app.idp.docker.localhost/")
-- ,  ("s1", "CLIENT_ID", "CLIENT_SECRET", "HOST"),  ("s2", "CLIENT_ID", "CLIENT_SECRET", "HOST"), ("s1", "CLIENT_ID", "CLIENT_SECRET", "HOST"),  ("s1", "CLIENT_ID", "CLIENT_SECRET", "HOST"),  ("s2", "CLIENT_ID", "CLIENT_SECRET", "HOST")
