insert into qc_modules(name_lang_key,desc_lang_key,sort,module_id)
values('module_expenses_general','module_expenses_general_desc',5,'expenses_caja');

insert into qc_permissions(permission_id, module_id, location_id) values('expenses_caja','expenses_caja',1);

insert into qc_grants(permission_id,person_id,menu_group)values('expenses_caja',1,'home');

