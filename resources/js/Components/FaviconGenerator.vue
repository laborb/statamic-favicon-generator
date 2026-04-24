<template>
    <div>
        <ui-publish-container
            ref="container"
            name="base"
            :blueprint="blueprint"
            :meta="meta"
            v-model="values"
        >
            <div class="max-w-5xl 3xl:max-w-6xl mx-auto">
                <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 max-md:pb-8 md:py-8">
                    <div class="md:flex-1">
                        <h1 class="text-[25px] leading-[1.25] st-text-legibility font-medium antialiased">Favicon Generator</h1>
                        <p class="mt-1 text-sm text-gray-600">Last generated: {{ values.generated_at }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <button class="relative inline-flex items-center justify-center whitespace-nowrap shrink-0 font-medium antialiased cursor-pointer no-underline disabled:cursor-not-allowed bg-linear-to-b from-primary/90 to-primary hover:bg-primary-hover text-white disabled:opacity-60 border border-primary-border shadow-ui-md inset-shadow-2xs inset-shadow-white/25 px-4 h-10 text-sm gap-2 rounded-lg" @click="save()">{{ generate }}</button>
                    </div>
                </header>

                <ui-publish-tabs />
            </div>
        </ui-publish-container>

        <ui-modal
            :open="isOpen"
            @dismissed="closeModal"
        >
            <div class="p-6 flex flex-col items-center gap-4 min-w-[18rem]">
                <template v-if="modalStatus === 'loading'">
                    <h2 class="text-lg font-medium">{{ modalLoadingTitle || 'Generating Favicons…' }}</h2>
                    <div role="status" class="mt-2">
                        <svg aria-hidden="true" class="w-12 h-12 animate-spin" viewBox="0 0 100 101" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#e5e7eb"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                        </svg>
                        <span class="sr-only">{{ modalLoadingTitle || 'Loading…' }}</span>
                    </div>
                    <p class="text-sm text-gray-500">{{ modalLoadingHint || 'This may take a few seconds.' }}</p>
                </template>
                <template v-else-if="modalStatus === 'error'">
                    <h2 class="text-lg font-medium">{{ modalErrorTitle || 'Generation failed' }}</h2>
                    <p class="text-sm text-red-600 text-center max-w-xs">{{ modalError }}</p>
                    <button class="relative inline-flex items-center justify-center whitespace-nowrap shrink-0 font-medium antialiased cursor-pointer no-underline bg-linear-to-b from-white to-gray-50 hover:to-gray-100 text-gray-900 border border-gray-300 shadow-ui-sm px-4 h-9 text-sm gap-2 rounded-lg" @click="closeModal">{{ modalClose || 'Close' }}</button>
                </template>
            </div>
        </ui-modal>
    </div>
</template>
<script>
export default {
    props: ['blueprint', 'meta', 'initialValues', 'generate', 'modalLoadingTitle', 'modalLoadingHint', 'modalErrorTitle', 'modalClose'],
    data() {
        return {
            values: this.initialValues,
            isOpen: false,
            modalStatus: 'loading',
            modalError: null,
        }
    },
    methods: {
        save() {
            this.isOpen = true;
            this.modalStatus = 'loading';
            this.modalError = null;

            this.$axios.post('/cp/favicon-generator/update', this.values)
            .then((response) => {
                if (response.data.status && response.data.status === 'success') {
                    this.isOpen = false;
                    this.$toast.success(response.data.msg);
                    this.$dirty.remove();
                } else {
                    this.modalStatus = 'error';
                    this.modalError = response.data.msg || 'An unknown error occurred.';
                }
            })
            .catch((error) => {
                this.modalStatus = 'error';
                this.modalError = error?.response?.data?.message || error?.message || 'Unexpected error.';
            });
        },
        closeModal() {
            this.isOpen = false;
        }
    }
}
</script>