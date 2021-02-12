CREATE DATABASE IF NOT EXISTS api_rest_servicio_auto;
USE api_rest_servicio_auto;

CREATE TABLE propietarios(
id              int(255) auto_increment not null,
apellido        varchar(50) not null,
nombre          varchar(50) not null,
documento       bigint not null,
direccion       varchar(255) not null,
telefono        varchar(50) not null,
created_at      datetime DEFAULT CURRENT_TIMESTAMP,
updated_at      datetime DEFAULT CURRENT_TIMESTAMP,    
CONSTRAINT pk_propietarios PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE autos(
id              int(255) auto_increment not null,
marca           varchar(50) not null,
modelo          varchar(50) not null,
anio            int not null,
patente         varchar(50) not null,
color           varchar(50) not null,
propietario_id  int(255) not null,
created_at      datetime DEFAULT CURRENT_TIMESTAMP,
updated_at      datetime DEFAULT CURRENT_TIMESTAMP,    
CONSTRAINT pk_autos PRIMARY KEY(id),
CONSTRAINT fk_propietarios FOREIGN KEY(propietario_id) REFERENCES propietarios(id)
)ENGINE=InnoDb;



CREATE TABLE servicios(
id              int(255) auto_increment not null,
servicio        varchar(50) not null,
costo           double not null,
created_at      datetime DEFAULT CURRENT_TIMESTAMP,
updated_at      datetime DEFAULT CURRENT_TIMESTAMP,    
CONSTRAINT pk_propietarios PRIMARY KEY(id)
)ENGINE=InnoDb;


CREATE TABLE transacciones(
id                              int(255) auto_increment not null,
auto_id                         int(255) not null,
servicio_id                     int(255) not null,
costo_servicio_transaccion      double not null,
created_at                      datetime DEFAULT CURRENT_TIMESTAMP,
updated_at                      datetime DEFAULT CURRENT_TIMESTAMP,    
CONSTRAINT pk_transacciones PRIMARY KEY(id),
CONSTRAINT fk_autos FOREIGN KEY(auto_id) REFERENCES autos(id),
CONSTRAINT fk_servicios FOREIGN KEY(servicio_id) REFERENCES servicios(id)
)ENGINE=InnoDb;