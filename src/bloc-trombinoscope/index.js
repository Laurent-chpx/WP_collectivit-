import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, Spinner } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

registerBlockType('wpcollectivites/trombinoscope', {
    title: 'Trombinoscope',
    icon: 'businessperson',
    category: 'wp-collectivites',
    keywords: ['trombinoscope', 'membre', 'equipe', 'municipale', 'élu'],

    attributes: {
        membre_id: { type: 'number', default: 0 }
    },

    edit: ({ attributes, setAttributes }) => {
        const { membre_id } = attributes;
        const [membres, setMembres] = useState([]);
        const [membreDetails, setMembreDetails] = useState(null);
        const [isLoadingMembres, setIsLoadingMembres] = useState(true);
        const [isLoadingDetails, setIsLoadingDetails] = useState(false);
        const [couleurFond, setCouleurFond] = useState('#ffffff');

        useEffect(() => {
            if (window.wpcTrombinoscope && window.wpcTrombinoscope.couleur_fond) {
                setCouleurFond(window.wpcTrombinoscope.couleur_fond);
            }

            apiFetch({ path: '/wp/v2/membre-equipe-muni?per_page=100&orderby=title&order=asc' })
                .then((posts) => {
                    const options = [{ label: '-- Sélectionner un membre --', value: 0 }];
                    posts.forEach((post) => {
                        options.push({
                            label: post.title.rendered,
                            value: post.id
                        });
                    });
                    setMembres(options);
                    setIsLoadingMembres(false);
                })
                .catch((err) => {
                    console.error('Erreur lors du chargement des membres:', err);
                    setIsLoadingMembres(false);
                });
        }, []);

        useEffect(() => {
            if (membre_id && membre_id !== 0) {
                setIsLoadingDetails(true);
                apiFetch({ path: `/wp/v2/membre-equipe-muni/${membre_id}?_embed` })
                    .then((membre) => {
                        const details = {
                            id: membre.id,
                            titre: membre.title.rendered,
                            fonction: membre.meta?._wpc_fonction || '',
                            infos_supp: membre.meta?._wpc_informations_supplementaires || '',
                            image: null
                        };

                        if (membre._embedded && membre._embedded['wp:featuredmedia'] && membre._embedded['wp:featuredmedia'][0]) {
                            const media = membre._embedded['wp:featuredmedia'][0];
                            if (media.media_details && media.media_details.sizes) {
                                const size = media.media_details.sizes.medium || media.media_details.sizes.full;
                                if (size) {
                                    details.image = size.source_url;
                                }
                            }
                        }

                        setMembreDetails(details);
                        setIsLoadingDetails(false);
                    })
                    .catch((err) => {
                        console.error('Erreur lors du chargement des détails du membre:', err);
                        setIsLoadingDetails(false);
                        setMembreDetails({
                            id: membre_id,
                            titre: 'Membre sélectionné',
                            fonction: '',
                            infos_supp: '',
                            image: null
                        });
                    });
            } else {
                setMembreDetails(null);
            }
        }, [membre_id]);

        const blockProps = useBlockProps({
            className: 'bloc-trombinoscope',
            style: membreDetails ? { backgroundColor: couleurFond } : {}
        });

        return (
            <>
                <InspectorControls>
                    <PanelBody title="Sélection du membre" initialOpen={true}>
                        {isLoadingMembres ? (
                            <Spinner />
                        ) : (
                            <SelectControl
                                label="Membre à afficher"
                                help="Sélectionnez un membre de l'équipe municipale"
                                value={membre_id}
                                options={membres}
                                onChange={(value) => setAttributes({ membre_id: parseInt(value) })}
                            />
                        )}
                        <div style={{
                            marginTop: '20px',
                            padding: '10px',
                            backgroundColor: '#f0f0f0',
                            borderRadius: '4px'
                        }}>
                            <p style={{ margin: 0, fontSize: '12px', color: '#666' }}>
                                <strong>Note :</strong> La couleur de fond du trombinoscope
                                est définie dans les options générales du plugin.
                            </p>
                            <p style={{ margin: '5px 0 0 0', fontSize: '12px' }}>
                                Couleur actuelle :
                                <span style={{
                                    display: 'inline-block',
                                    width: '20px',
                                    height: '20px',
                                    backgroundColor: couleurFond,
                                    border: '1px solid #ccc',
                                    borderRadius: '3px',
                                    verticalAlign: 'middle',
                                    marginLeft: '5px'
                                }}></span>
                                {' '}{couleurFond}
                            </p>
                        </div>
                    </PanelBody>
                </InspectorControls>

                <div {...blockProps}>
                    {!membreDetails ? (
                        <div style={{
                            padding: '40px 20px',
                            backgroundColor: '#f0f0f0',
                            border: '2px dashed #ccc',
                            textAlign: 'center',
                            color: '#666'
                        }}>
                            <span className="dashicons dashicons-businessperson"
                                  style={{ fontSize: '48px', color: '#999' }}></span>
                            <p style={{ marginTop: '10px', marginBottom: 0 }}>
                                <strong>Bloc Trombinoscope</strong>
                            </p>
                            <p style={{ margin: '10px 0 0 0' }}>
                                Veuillez sélectionner un membre de l'équipe municipale
                            </p>
                        </div>
                    ) : isLoadingDetails ? (
                        <div style={{ textAlign: 'center', padding: '40px' }}>
                            <Spinner />
                            <p>Chargement des informations...</p>
                        </div>
                    ) : (
                        <>
                            <div className="bloc-trombinoscope--photo" style={{
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                height: '300px',
                                backgroundColor: '#f9f9f9'
                            }}>
                                {membreDetails.image ? (
                                    <img src={membreDetails.image}
                                         alt={membreDetails.titre}
                                         style={{
                                             height: '100%',
                                             width: 'auto',
                                             objectFit: 'contain'
                                         }} />
                                ) : (
                                    <span className="dashicons dashicons-businessperson"
                                          style={{ fontSize: '150px', color: '#ccc' }}></span>
                                )}
                            </div>

                            <div className="bloc-trombinoscope--contenu" style={{
                                textAlign: 'center',
                                padding: '20px'
                            }}>
                                <h3>{membreDetails.titre}</h3>

                                {membreDetails.fonction && (
                                    <p className="bloc-trombinoscope--fonction">
                                        <strong>{membreDetails.fonction}</strong>
                                    </p>
                                )}

                                {membreDetails.infos_supp && (
                                    <p className="bloc-trombinoscope--infos"
                                       dangerouslySetInnerHTML={{ __html: membreDetails.infos_supp }} />
                                )}

                                {!membreDetails.fonction && !membreDetails.infos_supp && (
                                    <p style={{ color: '#999', fontStyle: 'italic' }}>
                                        Les informations détaillées seront affichées
                                        si elles sont renseignées dans la fiche du membre.
                                    </p>
                                )}
                            </div>
                        </>
                    )}
                </div>
            </>
        );
    },

    save: () => null
});