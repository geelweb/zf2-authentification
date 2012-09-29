CREATE TABLE auth_user (
    id serial primary key,
    username varchar(40) not null,
    password varchar(100) not null,
    email varchar(250) not null,
    is_super_user boolean not null default 'f',
    created_on timestamp not null default now()
);

