-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema cc500217_db
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema cc500217_db
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `cc500217_db` DEFAULT CHARACTER SET utf8 ;
USE `cc500217_db` ;

-- -----------------------------------------------------
-- Table `cc500217_db`.`region`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cc500217_db`.`region` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cc500217_db`.`comuna`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cc500217_db`.`comuna` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(200) NOT NULL,
  `region_id` INT NOT NULL,
  PRIMARY KEY (`id`, `region_id`),
  INDEX `fk_comuna_region1_idx` (`region_id` ASC),
  CONSTRAINT `fk_comuna_region1`
    FOREIGN KEY (`region_id`)
    REFERENCES `cc500217_db`.`region` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cc500217_db`.`especialidad`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cc500217_db`.`especialidad` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `descripcion` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cc500217_db`.`solicitud_atencion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cc500217_db`.`solicitud_atencion` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre_solicitante` VARCHAR(30) NOT NULL,
  `especialidad_id` INT NOT NULL,
  `sintomas` VARCHAR(500) NULL,
  `twitter` VARCHAR(80) NULL,
  `email` VARCHAR(80) NOT NULL,
  `celular` VARCHAR(15) NULL,
  `comuna_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_viaje_comuna1_idx` (`comuna_id` ASC),
  INDEX `fk_solicitud_atencion_especialidad1_idx` (`especialidad_id` ASC),
  CONSTRAINT `fk_viaje_comuna1`
    FOREIGN KEY (`comuna_id`)
    REFERENCES `cc500217_db`.`comuna` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_solicitud_atencion_especialidad1`
    FOREIGN KEY (`especialidad_id`)
    REFERENCES `cc500217_db`.`especialidad` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cc500217_db`.`medico`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cc500217_db`.`medico` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(30) NOT NULL,
  `experiencia` VARCHAR(500) NULL,
  `comuna_id` INT NOT NULL,
  `twitter` VARCHAR(80) NULL,
  `email` VARCHAR(80) NOT NULL,
  `celular` VARCHAR(15) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_encargo_comuna1_idx` (`comuna_id` ASC),
  CONSTRAINT `fk_encargo_comuna1`
    FOREIGN KEY (`comuna_id`)
    REFERENCES `cc500217_db`.`comuna` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cc500217_db`.`foto_medico`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cc500217_db`.`foto_medico` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ruta_archivo` VARCHAR(300) NOT NULL,
  `nombre_archivo` VARCHAR(300) NULL,
  `medico_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_foto_medico_medico1_idx` (`medico_id` ASC),
  CONSTRAINT `fk_foto_medico_medico1`
    FOREIGN KEY (`medico_id`)
    REFERENCES `cc500217_db`.`medico` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cc500217_db`.`especialidad_medico`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cc500217_db`.`especialidad_medico` (
  `medico_id` INT NOT NULL,
  `especialidad_id` INT NOT NULL,
  INDEX `fk_especialidad_medico_medico1_idx` (`medico_id` ASC),
  INDEX `fk_especialidad_medico_especialidad1_idx` (`especialidad_id` ASC),
  PRIMARY KEY (`medico_id`, `especialidad_id`),
  CONSTRAINT `fk_especialidad_medico_medico1`
    FOREIGN KEY (`medico_id`)
    REFERENCES `cc500217_db`.`medico` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_especialidad_medico_especialidad1`
    FOREIGN KEY (`especialidad_id`)
    REFERENCES `cc500217_db`.`especialidad` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cc500217_db`.`archivo_solicitud`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cc500217_db`.`archivo_solicitud` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ruta_archivo` VARCHAR(300) NOT NULL,
  `nombre_archivo` VARCHAR(300) NULL,
  `mimetype` VARCHAR(100) NOT NULL,
  `solicitud_atencion_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_archivo_solicitud_solicitud_atencion1_idx` (`solicitud_atencion_id` ASC),
  CONSTRAINT `fk_archivo_solicitud_solicitud_atencion1`
    FOREIGN KEY (`solicitud_atencion_id`)
    REFERENCES `cc500217_db`.`solicitud_atencion` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
