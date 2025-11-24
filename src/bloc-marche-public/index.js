import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

registerBlockType('wpcollectivites/marche-public', {
    title: 'Marché Public',
    icon: 'megaphone',
    category: 'wp-collectivites',
    keywords: ['marché', 'public', 'appel', 'offre'],

    attributes: {
        intitule: { type: 'string', default: '' },
        date_publication: { type: 'string', default: '' },
        date_cloture: { type: 'string', default: '' },
        type_marche: { type: 'string', default: '' },
        lien: { type: 'string', default: '' },
        profil_acheteur: { type: 'string', default: '' },
        avis_complet: { type: 'string', default: '' }
    },

    edit: ({ attributes, setAttributes, className }) => {
        const {
            intitule, date_publication, date_cloture,
            type_marche, lien, profil_acheteur, avis_complet
        } = attributes;

        return (
            <>
                <InspectorControls>
                    <PanelBody title="Configuration du marché public" initialOpen={true}>
                        <TextControl
                            label="Date de publication"
                            help="Format : AAAA-MM-JJ"
                            value={date_publication}
                            onChange={(value) => setAttributes({ date_publication: value })}
                            type="date"
                        />
                        <TextControl
                            label="Date et heure de clôture"
                            help="Format : AAAA-MM-JJ HH:MM"
                            value={date_cloture}
                            onChange={(value) => setAttributes({ date_cloture: value })}
                            type="datetime-local"
                        />
                        <TextControl
                            label="Type de marché"
                            value={type_marche}
                            onChange={(value) => setAttributes({ type_marche: value })}
                            placeholder="Ex: Travaux, Services, Fournitures..."
                        />
                        <TextControl
                            label="Lien vers la plateforme"
                            value={lien}
                            onChange={(value) => setAttributes({ lien: value })}
                            type="url"
                            placeholder="https://..."
                        />
                        <TextControl
                            label="Profil acheteur"
                            value={profil_acheteur}
                            onChange={(value) => setAttributes({ profil_acheteur: value })}
                        />
                    </PanelBody>
                </InspectorControls>

                <div className={`${className} bloc-marche-public`}>
                    <RichText
                        tagName="h3"
                        value={intitule}
                        onChange={(value) => setAttributes({ intitule: value })}
                        placeholder="Titre du marché..."
                        allowedFormats={[]}
                    />

                    {date_publication && (
                        <p><strong>Date de publication : </strong>{date_publication}</p>
                    )}

                    {date_cloture && (
                        <p>
                            <strong>Date de clôture : </strong>
                            {new Date(date_cloture).toLocaleString('fr-FR')}
                            {new Date(date_cloture) < new Date() && (
                                <span style={{ color: 'red', marginLeft: '10px' }}>
                                    Marché clôturé
                                </span>
                            )}
                        </p>
                    )}

                    {type_marche && (
                        <p><strong>Type de marché : </strong>{type_marche}</p>
                    )}

                    <div className="wp-block-buttons">
                        <div className="wp-block-button">
                            <span className="wp-block-button__link">En savoir plus</span>
                        </div>
                    </div>

                    <div style={{
                        marginTop: '20px',
                        padding: '10px',
                        border: '1px dashed #ccc',
                        backgroundColor: '#f5f5f5'
                    }}>
                        <p style={{ marginTop: 0, fontWeight: 'bold', color: '#666' }}>
                            Avis complet (visible après clic sur "En savoir plus") :
                        </p>

                        {lien && (
                            <p>
                                <strong>Lien vers la plateforme : </strong>
                                <a href={lien} target="_blank" rel="noopener noreferrer">{lien}</a>
                            </p>
                        )}

                        {profil_acheteur && (
                            <p><strong>Profil acheteur : </strong>{profil_acheteur}</p>
                        )}

                        <RichText
                            tagName="div"
                            value={avis_complet}
                            onChange={(value) => setAttributes({ avis_complet: value })}
                            placeholder="Saisissez ici l'avis complet du marché..."
                            allowedFormats={['core/bold', 'core/italic', 'core/link']}
                        />
                    </div>
                </div>
            </>
        );
    },

    save: () => null
});