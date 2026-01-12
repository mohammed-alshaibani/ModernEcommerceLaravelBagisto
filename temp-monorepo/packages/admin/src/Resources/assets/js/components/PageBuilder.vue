<template>
    <div class="page-builder-wrapper">
        <slot v-if="loading"></slot>
        <div id="gjs" v-show="!loading"></div>
    </div>
</template>

<script>
import grapesjs from 'grapesjs';
import 'grapesjs/dist/css/grapes.min.css';

export default {
    name: 'v-page-builder',

    props: {
        pageId: {
            type: Number,
            required: true
        }
    },

    data() {
        return {
            editor: null,
            loading: true
        }
    },

    mounted() {
        this.initEditor();
    },

    methods: {
        async initEditor() {
            try {
                // Fetch existing data
                const response = await this.$axios.get(`cms/builder/${this.pageId}/data`);
                const pageData = response.data.page;
                const builderData = response.data.builder_data;

                this.loading = false;

                this.$nextTick(() => {
                    this.editor = grapesjs.init({
                        container: '#gjs',
                        height: '100%',
                        width: 'auto',
                        storageManager: false,
                        panels: {
                            defaults: [
                                {
                                    id: 'panel-switcher',
                                    el: '.panel__switcher',
                                    buttons: [
                                        {
                                            id: 'show-layers',
                                            active: false,
                                            className: 'fa fa-bars',
                                            command: 'show-layers',
                                            attributes: { title: 'Layers' }
                                        },
                                        {
                                            id: 'show-styles',
                                            active: true,
                                            className: 'fa fa-paint-brush',
                                            command: 'show-styles',
                                            attributes: { title: 'Styles' }
                                        },
                                        {
                                            id: 'show-traits',
                                            active: false,
                                            className: 'fa fa-cog',
                                            command: 'show-traits',
                                            attributes: { title: 'Settings' }
                                        }
                                    ]
                                },
                                {
                                    id: 'panel-devices',
                                    el: '.panel__devices',
                                    buttons: [
                                        {
                                            id: 'device-desktop',
                                            command: 'set-device-desktop',
                                            className: 'fa fa-desktop',
                                            active: true,
                                            attributes: { title: 'Desktop' }
                                        },
                                        {
                                            id: 'device-mobile',
                                            command: 'set-device-mobile',
                                            className: 'fa fa-mobile',
                                            attributes: { title: 'Mobile' }
                                        }
                                    ]
                                },
                                {
                                    id: 'panel-actions',
                                    el: '.panel__basic-actions',
                                    buttons: [
                                        {
                                            id: 'visibility',
                                            active: true,
                                            className: 'fa fa-square-o',
                                            command: 'sw-visibility',
                                            context: 'sw-visibility',
                                            attributes: { title: 'View Components' }
                                        },
                                        {
                                            id: 'preview',
                                            className: 'fa fa-eye',
                                            command: 'preview',
                                            context: 'preview',
                                            attributes: { title: 'Preview' }
                                        },
                                        {
                                            id: 'undo',
                                            className: 'fa fa-undo',
                                            command: 'core:undo',
                                            attributes: { title: 'Undo' }
                                        },
                                        {
                                            id: 'redo',
                                            className: 'fa fa-repeat',
                                            command: 'core:redo',
                                            attributes: { title: 'Redo' }
                                        },
                                        {
                                            id: 'canvas-clear',
                                            className: 'fa fa-trash',
                                            command: 'canvas-clear',
                                            attributes: { title: 'Clear Canvas' }
                                        }
                                    ]
                                }
                            ]
                        },
                        blockManager: {
                            appendTo: '.blocks-container'
                        },
                        layerManager: {
                            appendTo: '.layers-container'
                        },
                        styleManager: {
                            appendTo: '.styles-container'
                        },
                        traitManager: {
                            appendTo: '.traits-container'
                        },
                        selectorManager: {
                            appendTo: '.styles-container'
                        },
                        deviceManager: {
                            devices: [{
                                name: 'Desktop',
                                width: '',
                            }, {
                                name: 'Mobile',
                                width: '320px',
                                widthMedia: '480px',
                            }]
                        },
                        plugins: [
                            'gjs-preset-webpage',
                            'gjs-blocks-basic'
                        ]
                    });

                    // Define Commands
                    this.editor.Commands.add('show-layers', {
                        run(editor) {
                            document.querySelector('.layers-container').style.display = 'block';
                            document.querySelector('.styles-container').style.display = 'none';
                            document.querySelector('.traits-container').style.display = 'none';
                        }
                    });

                    this.editor.Commands.add('show-styles', {
                        run(editor) {
                            document.querySelector('.layers-container').style.display = 'none';
                            document.querySelector('.styles-container').style.display = 'block';
                            document.querySelector('.traits-container').style.display = 'none';
                        }
                    });

                    this.editor.Commands.add('show-traits', {
                        run(editor) {
                            document.querySelector('.layers-container').style.display = 'none';
                            document.querySelector('.styles-container').style.display = 'none';
                            document.querySelector('.traits-container').style.display = 'block';
                        }
                    });

                    this.editor.Commands.add('set-device-desktop', {
                        run: editor => editor.setDevice('Desktop')
                    });

                    this.editor.Commands.add('set-device-mobile', {
                        run: editor => editor.setDevice('Mobile')
                    });
                    
                    this.editor.Commands.add('canvas-clear', {
                        run: editor => {
                            if(confirm('Are you sure you want to clear the canvas?')) {
                                editor.DomComponents.clear();
                            }
                        }
                    });

                    // Load initial data if available
                    if (builderData) {
                        this.editor.loadProjectData(builderData);
                    } else if (pageData.html_content) {
                        this.editor.setComponents(pageData.html_content);
                    }

                    // Expose editor instance
                    this.$emit('editor-loaded', this.editor);
                });

            } catch (error) {
                console.error('Failed to load builder data:', error);
                this.$emitter.emit('add-flash', { type: 'error', message: error.message });
            }
        },

        async savePageData() {
            if (!this.editor) return;

            const html = this.editor.getHtml();
            const css = this.editor.getCss();
            const projectData = this.editor.getProjectData();

            try {
                await this.$axios.post(`cms/builder/${this.pageId}/save`, {
                    html_content: html + `<style>${css}</style>`,
                    builder_data: JSON.stringify(projectData)
                });

                this.$emitter.emit('add-flash', { type: 'success', message: 'Page saved successfully' });
            } catch (error) {
                console.error('Failed to save page:', error);
                this.$emitter.emit('add-flash', { type: 'error', message: error.message });
            }
        }
    }
}
</script>

<style scoped>
.page-builder-wrapper {
    height: 100%;
    width: 100%;
}
</style>
