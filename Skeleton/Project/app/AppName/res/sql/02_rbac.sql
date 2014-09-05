INSERT INTO userdata (userid, name, password, is_admin) VALUES ('admin', 'Administrator', '1', true);
INSERT INTO userdata (userid, name, password, is_admin) VALUES ('user', 'Test User', '1', false);

INSERT INTO roledata (name) VALUES ('Default');
INSERT INTO roledata (name) VALUES ('Administrator');
INSERT INTO roledata (name) VALUES ('CRUD');

-- default access
INSERT INTO accessdata (name) VALUES ('Home/index');
INSERT INTO accessdata (name) VALUES ('Logout/index');
INSERT INTO accessdata (name) VALUES ('User/profile');

-- admin access
INSERT INTO accessdata (name) VALUES ('Admin/index');

-- Userdata table CRUD access
INSERT INTO accessdata (name) VALUES ('Userdata/index');
INSERT INTO accessdata (name) VALUES ('Userdata/edit');
INSERT INTO accessdata (name) VALUES ('Userdata/pager');
INSERT INTO accessdata (name) VALUES ('Userdata/delete');

-- Userdata relations table CRUD access
INSERT INTO accessdata (name) VALUES ('Userdata/tabPager');
INSERT INTO accessdata (name) VALUES ('Userdata/tabForm');
INSERT INTO accessdata (name) VALUES ('Userdata/tabDelete');

-- Roledata table CRUD access
INSERT INTO accessdata (name) VALUES ('Roledata/index');
INSERT INTO accessdata (name) VALUES ('Roledata/edit');
INSERT INTO accessdata (name) VALUES ('Roledata/pager');
INSERT INTO accessdata (name) VALUES ('Roledata/delete');

-- Accessdata table CRUD access
INSERT INTO accessdata (name) VALUES ('Accessdata/index');
INSERT INTO accessdata (name) VALUES ('Accessdata/edit');
INSERT INTO accessdata (name) VALUES ('Accessdata/pager');
INSERT INTO accessdata (name) VALUES ('Accessdata/delete');

-- Master Table CRUD access
INSERT INTO accessdata (name) VALUES ('TableMaster/index');
INSERT INTO accessdata (name) VALUES ('TableMaster/edit');
INSERT INTO accessdata (name) VALUES ('TableMaster/pager');
INSERT INTO accessdata (name) VALUES ('TableMaster/delete');

-- Default Roles
INSERT INTO role_access (role_id, access_id) 
  SELECT r.id, a.id FROM roledata r, accessdata a WHERE r.name = 'Default' 
    AND a.name IN ('Home/index', 'Logout/index', 'User/profile') ;

-- Multi-tables CRUD role
INSERT INTO role_access (role_id, access_id) 
  SELECT r.id, a.id FROM roledata r, accessdata a WHERE r.name = 'CRUD' 
    AND a.name LIKE 'TableMaster%';

--
-- ADD NEW ROLES HERE
--

-- Administrator Roles
-- Other accessdata not included in above roles are considered Administrator only roles
INSERT INTO role_access (role_id, access_id)
  SELECT r.id, a.id FROM roledata r, accessdata a WHERE r.name = 'Administrator'
    AND a.id NOT IN (SELECT access_id FROM role_access);

-- Give Administrators access to everything 
INSERT INTO user_role (user_id, role_id) 
  SELECT u.id, r.id FROM userdata u, roledata r 
    WHERE u.is_admin IS TRUE;

-- Give Regular users access to default menu
INSERT INTO user_role (user_id, role_id) 
  SELECT u.id, r.id FROM userdata u, roledata r 
    WHERE u.is_admin IS FALSE AND r.name = 'Default';

