SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `blocks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blocks` ;

CREATE  TABLE IF NOT EXISTS `blocks` (
  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `code_name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`ID`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `blocks_sets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blocks_sets` ;

CREATE  TABLE IF NOT EXISTS `blocks_sets` (
  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `parent_ID` INT UNSIGNED NULL DEFAULT NULL ,
  `pregenerated_structure` TEXT NULL DEFAULT NULL ,
  `created_on` DATETIME NULL DEFAULT NULL ,
  `created_by` INT NULL DEFAULT NULL ,
  `edited_on` DATETIME NULL DEFAULT NULL ,
  `edited_by` INT NULL DEFAULT NULL ,
  PRIMARY KEY (`ID`) ,
  INDEX `FK__blocks_sets__parent_ID` (`parent_ID` ASC) ,
  CONSTRAINT `FK__blocks_sets__parent_ID`
    FOREIGN KEY (`parent_ID` )
    REFERENCES `blocks_sets` (`ID` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `web_pages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `web_pages` ;

CREATE  TABLE IF NOT EXISTS `web_pages` (
  `ID` INT NOT NULL AUTO_INCREMENT ,
  `parent_ID` INT UNSIGNED NULL DEFAULT NULL ,
  `blocks_set_ID` INT UNSIGNED NOT NULL ,
  `entity_type` VARCHAR(255) NULL DEFAULT NULL ,
  `entity_ID` INT UNSIGNED NULL ,
  `url_name` VARCHAR(255) NOT NULL ,
  `created_on` DATETIME NULL DEFAULT NULL ,
  `created_by` INT NULL DEFAULT NULL ,
  `edited_on` DATETIME NULL DEFAULT NULL ,
  `edited_by` INT NULL DEFAULT NULL ,
  PRIMARY KEY (`ID`) ,
  INDEX `FK__web_pages__parent_ID` (`parent_ID` ASC) ,
  INDEX `FK__web_pages__blocks_set_ID` (`blocks_set_ID` ASC) ,
  CONSTRAINT `FK__web_pages__parent_ID`
    FOREIGN KEY (`parent_ID` )
    REFERENCES `web_pages` (`blocks_set_ID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK__web_pages__blocks_set_ID`
    FOREIGN KEY (`blocks_set_ID` )
    REFERENCES `blocks_sets` (`ID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `blocks_templates`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blocks_templates` ;

CREATE  TABLE IF NOT EXISTS `blocks_templates` (
  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `block_ID` INT UNSIGNED NOT NULL ,
  `filename` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`ID`) ,
  INDEX `FK__blocks_templates__block_ID` (`block_ID` ASC) ,
  CONSTRAINT `FK_blocks_templates_block_ID`
    FOREIGN KEY (`block_ID` )
    REFERENCES `blocks` (`ID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `blocks_templates_slots`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blocks_templates_slots` ;

CREATE  TABLE IF NOT EXISTS `blocks_templates_slots` (
  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `template_ID` INT UNSIGNED NOT NULL ,
  `code_name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`ID`) ,
  INDEX `FK__blocks_slots__block_ID` (`template_ID` ASC) ,
  UNIQUE INDEX `UNQ__blocks_slots__name` (`template_ID` ASC, `code_name` ASC) ,
  CONSTRAINT `FK_blocks_slots_block_ID`
    FOREIGN KEY (`template_ID` )
    REFERENCES `blocks_templates` (`ID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB, 
COMMENT = 'erger\n' ;


-- -----------------------------------------------------
-- Table `blocks_instances`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blocks_instances` ;

CREATE  TABLE IF NOT EXISTS `blocks_instances` (
  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `blocks_set_ID` INT UNSIGNED NOT NULL ,
  `template_ID` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`ID`) ,
  INDEX `FK__blocks_instances__template_ID` (`template_ID` ASC) ,
  INDEX `FK__blocks_instances__blocks_set_ID` (`blocks_set_ID` ASC) ,
  CONSTRAINT `FK_blocks_instances_tempate_ID`
    FOREIGN KEY (`template_ID` )
    REFERENCES `blocks_templates` (`ID` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `FK_blocks_instances_blocks_set_ID`
    FOREIGN KEY (`blocks_set_ID` )
    REFERENCES `blocks_sets` (`ID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `blocks_instances_subblocks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blocks_instances_subblocks` ;

CREATE  TABLE IF NOT EXISTS `blocks_instances_subblocks` (
  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parent_instance_ID` INT UNSIGNED NOT NULL ,
  `parent_slot_ID` INT UNSIGNED NOT NULL ,
  `position` INT UNSIGNED NOT NULL ,
  `inserted_instance_ID` INT UNSIGNED NULL ,
  PRIMARY KEY (`ID`) ,
  INDEX `FK__blocks_instances_subblocks__inserted_instance_ID` (`inserted_instance_ID` ASC) ,
  INDEX `FK__blocks_instances_subblocks__parent_instance_ID` (`parent_instance_ID` ASC) ,
  INDEX `FK__blocks_instances_subblocks__parent_slot_ID` (`parent_slot_ID` ASC) ,
  INDEX `UNQ__blocks_instances_subblocks__slot_position` (`parent_instance_ID` ASC, `parent_slot_ID` ASC, `position` ASC) ,
  CONSTRAINT `FK_blocks_instances_subblocks_inserted_instance_ID`
    FOREIGN KEY (`inserted_instance_ID` )
    REFERENCES `blocks_instances` (`ID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_blocks_instances_subblocks_parent_instance_ID`
    FOREIGN KEY (`parent_instance_ID` )
    REFERENCES `blocks_instances` (`ID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_blocks_instances_subblocks_parent_slot_ID`
    FOREIGN KEY (`parent_slot_ID` )
    REFERENCES `blocks_templates_slots` (`ID` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `blocks_data_requirements`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blocks_data_requirements` ;

CREATE  TABLE IF NOT EXISTS `blocks_data_requirements` (
  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `block_ID` INT UNSIGNED NOT NULL ,
  `property` VARCHAR(255) NOT NULL ,
  `data_type` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`ID`) ,
  INDEX `FK__blocks_data_requirements__block_ID` (`block_ID` ASC) ,
  CONSTRAINT `FK_blocks_data_dependencies_block_ID`
    FOREIGN KEY (`block_ID` )
    REFERENCES `blocks` (`ID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `blocks_data_requirements_providers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blocks_data_requirements_providers` ;

CREATE  TABLE IF NOT EXISTS `blocks_data_requirements_providers` (
  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `required_property_ID` INT UNSIGNED NOT NULL ,
  `provider_ID` INT UNSIGNED NOT NULL ,
  `provider_property` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`ID`) ,
  INDEX `FK__blocks_data_dependencies_providers__required_property_ID` (`required_property_ID` ASC) ,
  INDEX `FK__blocks_data_dependencies_providers__provider_ID` (`provider_ID` ASC) ,
  CONSTRAINT `FK_blocks_data_dependencies_providers_required_property_ID`
    FOREIGN KEY (`required_property_ID` )
    REFERENCES `blocks_data_requirements` (`ID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_blocks_data_dependencies_providers_provider_ID`
    FOREIGN KEY (`provider_ID` )
    REFERENCES `blocks` (`ID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `blocks_instances_data_inherited`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blocks_instances_data_inherited` ;

CREATE  TABLE IF NOT EXISTS `blocks_instances_data_inherited` (
  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `instance_ID` INT UNSIGNED NOT NULL ,
  `provider_instance_ID` INT UNSIGNED NOT NULL ,
  `provider_property_ID` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`ID`) ,
  INDEX `FK__blocks_instances_data_inherited__provider_instance_ID` (`provider_instance_ID` ASC) ,
  INDEX `FK__blocks_instances_data_inherited__instance_ID` (`instance_ID` ASC) ,
  INDEX `FK__blocks_instances_data_inherited__provider_property_ID` (`provider_property_ID` ASC) ,
  CONSTRAINT `FK_blocks_instances_data_inherited_provider_instance_ID`
    FOREIGN KEY (`provider_instance_ID` )
    REFERENCES `blocks_instances` (`ID` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `FK_blocks_instances_data_inherited_instance_ID`
    FOREIGN KEY (`instance_ID` )
    REFERENCES `blocks_instances` (`ID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_blocks_instances_data_inherited_provider_property_ID`
    FOREIGN KEY (`provider_property_ID` )
    REFERENCES `blocks_data_requirements_providers` (`ID` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `blocks_instances_data_constant`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blocks_instances_data_constant` ;

CREATE  TABLE IF NOT EXISTS `blocks_instances_data_constant` (
  `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `instance_ID` INT UNSIGNED NOT NULL ,
  `property_ID` INT UNSIGNED NOT NULL ,
  `value` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`ID`) ,
  INDEX `FK__blocks_instances_data_constant__instance_ID` (`instance_ID` ASC) ,
  INDEX `FK__blocks_instances_data_constant__property_ID` (`property_ID` ASC) ,
  CONSTRAINT `FK_blocks_instances_data_constant_instance_ID`
    FOREIGN KEY (`instance_ID` )
    REFERENCES `blocks_instances` (`ID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_blocks_instances_data_constant_property_ID`
    FOREIGN KEY (`property_ID` )
    REFERENCES `blocks_data_requirements` (`ID` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

