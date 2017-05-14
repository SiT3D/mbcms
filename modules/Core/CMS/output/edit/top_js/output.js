
mbcms.output = function ()
{};

mbcms.output.__class = 'MBCMS\\output';

/**
 * 
 * @param {type} idTemplate
 * @param {type} out_index
 * @param {type} callback
 * @returns {undefined}
 */
mbcms.output.remove = function (idTemplate, out_index, callback)
{
    mbcms.ajax(
            {
                data:
                        {
                            class: this.__class + '->remove',
                            idTemplate: idTemplate,
                            out_index: out_index
                        },
                success: function (msg)
                {
                    if (typeof callback == 'function')
                        callback.call(callback, msg);
                }
            });
};

/**
 * 
 * @param {type} idTemplate
 * @param {type} out_index
 * @param {type} data
 * @param {type} callback
 * @returns {undefined}
 */
mbcms.output.update = function (idTemplate, out_index, data, callback)
{
    mbcms.ajax(
            {
                method: 'POST',
                data:
                        {
                            class: this.__class + '->update',
                            idTemplate: idTemplate,
                            out_index: out_index,
                            data: data,
                        },
                success: function (msg)
                {
                    if (typeof callback == 'function')
                        callback.call(callback, msg);
                }
            });
};

/**
 * 
 * @param {type} idTemplate
 * @param {type} out_index
 * @param {type} new_class
 * @param {type} callback
 * @returns {undefined}
 */
mbcms.output.update_output_class = function (idTemplate, out_index, new_class, callback)
{
    mbcms.ajax(
            {
                data:
                        {
                            class: this.__class + '->update_output_class',
                            idTemplate: idTemplate,
                            out_index: out_index,
                            new_class: new_class,
                        },
                success: function (msg)
                {
                    if (typeof callback == 'function')
                        callback.call(callback, msg);
                }
            });
};

/**
 * 
 * @param {type} idTemplate
 * @param {type} data {out_index: data, out_index: data}
 * @param {type} callback
 * @returns {undefined}
 */
mbcms.output.update_array = function (idTemplate, data, callback)
{
    mbcms.ajax(
            {
                data:
                        {
                            class: this.__class + '->update_array',
                            idTemplate: idTemplate,
                            data: data
                        },
                success: function (msg)
                {
                    if (typeof callback == 'function')
                        callback.call(callback, msg);
                }
            });
};

/**
 * 
 * @param {type} idTemplate
 * @param {type} out_class
 * @param {type} data
 * @param {type} callback
 * @returns {undefined}
 */
mbcms.output.add = function (idTemplate, out_class, data, callback)
{
    mbcms.ajax(
            {
                data:
                        {
                            class: this.__class + '->add',
                            idTemplate: idTemplate,
                            out_class: out_class,
                            data: data
                        },
                success: function (msg)
                {
                    if (typeof callback == 'function')
                        callback.call(callback, msg);
                }
            });
};

/**
 * 
 * @param {type} idTemplate
 * @param {type} outputs
 * @param {type} callback
 * @returns {undefined}
 */
mbcms.output.add_array = function (idTemplate, outputs, callback)
{
    mbcms.ajax(
            {
                data:
                        {
                            class: this.__class + '->add_array',
                            idTemplate: idTemplate,
                            outputs: outputs,
                        },
                success: function (msg)
                {
                    if (typeof callback == 'function')
                        callback.call(callback, msg);
                }
            });
};

/**
 * 
 * @param {type} idTemplate
 * @param {type} indexis
 * @param {type} callback
 * @returns {undefined}
 */
mbcms.output.resort = function (idTemplate, indexis, callback)
{
    mbcms.ajax(
            {
                data:
                        {
                            class: this.__class + '->resort',
                            idTemplate: idTemplate,
                            indexis: indexis,
                        },
                success: function (msg)
                {
                    if (typeof callback == 'function')
                        callback.call(callback, msg);
                }
            });
};

