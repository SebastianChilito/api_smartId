/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


[
    {
        _id: null,
        nombre: null,
        descripcion: null,
        codigo_barras: null,
        es_servicio: false,
        es_producto: false,
        servicio: {
            tiempo_estimado: null,            
            categorias:[],
            empleado:{},
            con_iva: false,
            productos: []
        },
        producto:{
            categorias:[],
            unidad_medida:{},
            costo_unidad: null,
            iva: null,
            stock: null,
            descuento: null,
        },
        precio: null,
        aplica_impuestos: null,
    }
]