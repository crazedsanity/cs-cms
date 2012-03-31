
--begin;

CREATE TABLE cms_user_id_table (
	user_id serial NOT NULL PRIMARY KEY,
	display_name text NOT NULL,
	email text NOT NULL,
	is_active boolean DEFAULT true NOT NULL,
	date_created date DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE TABLE cms_status_table (
	status_id serial NOT NULL PRIMARY KEY,
	description text NOT NULL,
	page_is_active boolean NOT NULL,
	page_is_visible_anonymous boolean NOT NULL
);

CREATE TABLE cms_page_table (
	page_name text NOT NULL PRIMARY KEY,
	owner_user_id integer NOT NULL REFERENCES cms_user_table(user_id),
	create_date timestamp NOT NULL DEFAULT NOW(),
	content text NOT NULL,
	post_timestamp timestamp NOT NULL DEFAULT NOW(),
	title text NOT NULL,
	status_id integer NOT NULL REFERENCES cms_status_table(status_id)
);






------------------------------------
--  PERMISSIONS STUFF
--
--  NOTE: the default is DENY, so users are denied unless something specifically gives them access. 
--  NOTE2: I intended to use PHPGACL for this... 
------------------------------------





CREATE TABLE cms_permission_table (
	permission_id serial NOT NULL PRIMARY KEY,
	name varchar(32) NOT NULL UNIQUE,
	description text NOT NULL
);



-- Table that defines groups.
CREATE TABLE cms_group_table (
	group_id serial NOT NULL PRIMARY KEY,
	name varchar(32) NOT NULL UNIQUE,
	description text NOT NULL
);



