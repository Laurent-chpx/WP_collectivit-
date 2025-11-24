(function(wp){
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, SelectControl, Spinner } = wp.components;
    const { Fragment, useState, useEffect } = wp.element;
    const { apiFetch } = wp;
    const { __ } = wp.i18n;

    registerBlockType('wpcollectivites/actes-officiels', {
        title: 'Actes officiels',
        icon: 'pdf',
        category: 'wp-collectivites',
        keywords: ['actes', 'officiels', 'documents', 'arrêtés'],

        attributes: {
            type_actes: {
                type: 'number',
                default: 0
            }
        },

        edit: function(props){
            const { attributes, setAttributes } = props;
            const { type_actes } = attributes;
            const [typesActes, setTypesActes] = useState([]);
            const [isLoading, setIsLoading] = useState(true);
            const [selectedTypeName, setSelectedTypeName] = useState('');

            useEffect(() => {
                apiFetch({
                    path: '/wp/v2/type-acte?per_page=100'
                })
                .then((terms) => {
                    const options = [
                        { label: '-- Sélectionner un type d\'acte --', value: 0}
                    ];
                    terms.forEach((term) => {
                        options.push({
                            label: term.name,
                            value: term.id,
                        });

                        if(term.id === type_actes){
                            setSelectedTypeName(term.name);
                        }
                    });

                    setTypesActes(options);
                    setIsLoading(false);
                })
                .catch((err) => {
                    console.error('Erreur lors du chargement des types d\'actes:', err);
                    setIsLoading(false);
                });
            },[]);

            const onChangeType = (value) => {
                const numValue = parseInt(value);
                setAttributes({ type_actes: numValue });

                const selected = typesActes.find(type => type.value === numValue);
                if (selected) {
                    setSelectedTypeName(selected.label);
                }
            };

            const blockProps = useBlockProps({
                className: 'bloc-actes-officiels'
            });

            return (
                <Fragment>
                    <InspectorControls>
                        <PanelBody
                            title="Configuration des actes officiels"
                            initialOpen={true}
                        >
                            {isLoading ? (
                                <Spinner />
                            ) : (
                                <SelectControl
                                    label="Type d'actes à afficher"
                                    help="Sélectionnez la catégorie d'actes officiels à afficher"
                                    value={type_actes}
                                    options={typesActes}
                                    onChange={onChangeType}
                                />
                            )}
                        </PanelBody>
                    </InspectorControls>
                    <div {...blockProps}>
                        {type_actes === 0 ? (
                            <div style={{
                                padding: '20px',
                                backgroundColor: '#f0f0f0',
                                border: '2px dashed #ccc',
                                textAlign: 'center',
                                color: '#666'
                            }}>
                                <p style={{ margin: 0 }}>
                                    <strong>Bloc Actes Officiels</strong>
                                </p>
                                <p style={{ margin: '10px 0 0 0' }}>
                                    Veuillez sélectionner un type d'acte dans les paramètres du bloc
                                </p>
                            </div>
                        ):(
                            <div style={{
                            padding: '20px',
                            backgroundColor: '#f9f9f9',
                            border: '1px solid #ddd'
                        }}>
                        <h3 style={{ marginTop: 0 }}>
                            {selectedTypeName || `Type d'acte ID: ${type_actes}`}
                        </h3>
                        <p style={{
                            color: '#666',
                            fontStyle: 'italic',
                            marginBottom: 0
                        }}>
                            Les actes de type "{selectedTypeName}" seront affichés ici,
                            organisés par année.
                        </p>
                        <p style={{
                            fontSize: '12px',
                            color: '#999',
                            marginTop: '10px'
                        }}>
                            Note : Le contenu réel sera généré côté serveur lors de l'affichage de la page.
                        </p>
                    </div>
                        )}
                    </div>
                </Fragment>
            );
        },

        save:function(){
            return null;
        }
    });
})(window.wp);