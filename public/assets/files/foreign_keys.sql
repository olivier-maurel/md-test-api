ALTER TABLE `materiels` ADD FOREIGN KEY (type_id) REFERENCES type(type_id), ADD FOREIGN KEY (fabricant_id) REFERENCES fabricant(fabricant_id);
ALTER TABLE `references` ADD FOREIGN KEY (materiel_id) REFERENCES materiels(materiel_id);
ALTER TABLE `tarifs` ADD FOREIGN KEY (catalogue_id) REFERENCES catalogue(catalogue_id), ADD FOREIGN KEY (materiel_id) REFERENCES materiels(materiel_id);
ALTER TABLE `tarifs_grandeurs` ADD FOREIGN KEY (catalogue_id) REFERENCES catalogue(catalogue_id);
ALTER TABLE `type` ADD FOREIGN KEY (metier_id) REFERENCES metier(metier_id);