create table if not exists b_bemarketplace_applications (
	ID int(18) not null auto_increment,
  SITE_ID char(2) not null,
  CLIENT_ID varchar(255) not null,
  CLIENT_SECRET varchar(255) not null,
  HOST varchar(255) not null,

	PRIMARY KEY (ID)
);
