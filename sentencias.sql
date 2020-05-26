-- MEDICO
-- insertar un medico
INSERT INTO medico (nombre, experiencia, comuna_id, twitter, email, celular) VALUES (?, ?, ?, ?, ?, ?)
-- obtener los últimos 5 médicos insertados en la base de datos
SELECT id, nombre, experiencia, comuna_id, twitter, email, celular FROM medico ORDER BY id DESC LIMIT 5
-- obtiene la información de un medico de acuerdo a su ID
SELECT id, nombre, experiencia, comuna_id, twitter, email, celular FROM medico WHERE id=?

-- FOTO_MEDICO
-- insertar foto de un médico
INSERT INTO foto_medico (ruta_archivo, nombre_archivo, medico_id) VALUES (?, ?, ?)
-- obtener fotos de un médico
SELECT id, ruta_archivo, nombre_archivo, medico_id FROM foto_medico WHERE medico_id=?

-- ESPECIALIDAD_MEDICO
-- insertar una especialidad de un medico
INSERT INTO especialidad_medico (medico_id, especialidad_id) VALUES (?, ?)
-- obtener las especialidades de un medico
SELECT ESP.id, ESP.descripcion FROM especialidad ESP, especialidad_medico ESPME WHERE ESPME.especialidad_id=ESP.id AND ESPME.medico_id=?

-- SOLICITUD_ATENCION
-- insertar solicitud_atencion
INSERT INTO solicitud_atencion (nombre_solicitante, especialidad_id, sintomas, twitter, email, celular, comuna_id) VALUES (?, ?, ?, ?, ?, ?, ?)
-- obtener las últimas 5 solicitudes insertadas en la base de datos
SELECT id, nombre_solicitante, especialidad_id, sintomas, twitter, email, celular, comuna_id FROM solicitud_atencion ORDER BY id DESC LIMIT 5
-- obtener información de una solicitud
SELECT id, nombre_solicitante, especialidad_id, sintomas, twitter, email, celular, comuna_id FROM solicitud_atencion WHERE id=?

-- ARCHIVO SOLICITUD
-- insertar un archivo asociado a una solicitud
INSERT INTO archivo_solicitud (ruta_archivo, nombre_archivo, mimetype, solicitud_atencion_id) VALUES (?, ?, ?, ?)
-- obtener archivos de una solicitud
SELECT id, ruta_archivo, nombre_archivo, mimetype, solicitud_atencion_id FROM archivo_solicitud WHERE solicitud_atencion_id=?

