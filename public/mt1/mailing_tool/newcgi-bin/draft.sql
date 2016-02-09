create table designer
(
	designer_id tinyint(3) unsigned not null auto_increment primary key,
	designer_name varchar(20) not null 
) type=myisam;
insert into designer(designer_name) values('Carlos');
insert into designer(designer_name) values('Derrick');
insert into designer(designer_name) values('Neal');
#
create table draft_creative
(
	creative_id int(11) not null auto_increment primary key,
	advertiser_id int(11) not null,
	designer_id tinyint(3) unsigned not null,
	creative_name varchar(80) not null,
	status char(1) not null default 'A',
	html_code longblob, 
	assigned_date date,
	due_date date,
	updated_date date,
	completed char(1) not null default 'N',
	notes longblob,
	inactive_date date,
	thumbnail varchar(255)
) type=innodb;
create index draft_creative_ind1 on draft_creative(advertiser_id,assigned_date);
create index draft_creative_ind2 on draft_creative(designer_id,assigned_date);
create index draft_creative_ind3 on draft_creative(due_date);
create index draft_creative_ind4 on draft_creative(updated_date);
