SELECT 
    `id_estudiante`, 
    `es_cedula`, 
    `es_nombre_completo`, 
    `es_fec_nacim`,
    TIMESTAMPDIFF(YEAR, `es_fec_nacim`, CURDATE()) AS `edad`
FROM 
    `sw_estudiante`
WHERE 
    TIMESTAMPDIFF(YEAR, `es_fec_nacim`, CURDATE()) < 18;