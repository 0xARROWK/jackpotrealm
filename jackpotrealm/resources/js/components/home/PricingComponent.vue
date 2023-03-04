<template>
        <section class="flex flex-col lg:flex-row items-start items-center lg:justify-center w-full w-full lg:px-10 py-12 ">
            <article class="bg-white w-4/5 lg:w-custom mb-10 lg:px-4 px-6 py-10 text-center text-primary-dark rounded-lg">
                <h5 class="font-bold text-base">Tier 1</h5>
                <h2 class="pb-4 flex justify-center font-bold border-b border-gray-300">
                    <span class="text-3xl mt-6 mr-1">$</span>
                    <span class="text-6xl">5</span>
                </h2>
                <ul class="text-sm font-bold">
                    <li class="pt-4 pb-4 border-b border-gray-300">Emojis for tier 1</li>
                    <li class="pt-4 pb-4 border-b border-gray-300">Highlighted message in the chat</li>
                    <li class="pt-3 pb-4 border-b border-gray-300">Subscription badge</li>
                    <li class="pt-4 pb-4 border-b border-gray-300">Custom chat color</li>
                </ul>
                <button class="uppercase text-center text-sm mt-12 xl:px-24 px-12 sm:px-16 py-2 font-bold text-primary-very-light rounded-md" style="background-image:linear-gradient(90deg, #a3a8f0 0%, #696fdd 100%);">
                    Learn More
                </button>
            </article>
          <!-- linear-gradient(hsl(236, 72%, 79%) to hsl(237, 63%, 64%)) -->
            <article class="lg:w-custom w-4/5 mb-10 px-6 py-16 lg:-mt-6 text-white text-center rounded-lg" style="background-image:linear-gradient(90deg, #a3a8f0 0%, #696fdd 100%);">
                <h5 class="font-bold text-base ">Tier 2</h5>
                <h2 class="font-bold pb-4 mt-2 border-b border-gray-100 flex justify-center">
                    <span class="text-3xl mt-6 mr-1">$</span>
                    <span class="text-6xl ">10</span>
                </h2>
                <ul class=" text-sm font-bold">
                    <li class="pt-4 pb-4 border-b border-gray-300">Emojis for tier 1 and 2</li>
                    <li class="pt-4 pb-4 border-b border-gray-300">Highlighted message in the chat</li>
                    <li class="pt-3 pb-4 border-b border-gray-300">Subscription badge</li>
                    <li class="pt-4 pb-4 border-b border-gray-300">Custom chat color</li>
                </ul>
                <button class="uppercase text-center text-sm mt-10 xl:px-24 px-12 sm:px-16 py-2 rounded-md font-bold bg-primary-very-light text-primary-blue">
                    Learn More
                </button>
            </article>
            <article class="bg-white w-4/5 lg:w-custom mb-10 lg:px-4 px-6 py-10 text-center text-primary-dark rounded-lg">
                <h5 class="font-bold text-base">Tier 3</h5>
                <h2 class="flex justify-center pb-4 font-bold border-b border-gray-200">
                    <span class="text-3xl mt-6 mr-1">$</span>
                    <span class="text-6xl">15</span>
                </h2>
                <ul class="text-sm font-bold">
                    <li class="pt-4 pb-4 border-b border-gray-300">Emojis for tier 1, 2 and 3</li>
                    <li class="pt-4 pb-4 border-b border-gray-300">Highlighted message in the chat</li>
                    <li class="pt-3 pb-4 border-b border-gray-300">Subscription badge</li>
                    <li class="pt-4 pb-4 border-b border-gray-300">Custom chat color</li>
                </ul>
                <button class="uppercase text-center text-sm mt-12 xl:px-24 px-12 sm:px-16 py-2 rounded-md font-bold text-primary-very-light" style="background-image:linear-gradient(90deg, #a3a8f0 0%, #696fdd 100%);">
                    Learn More
                </button>
            </article>
        </section>

</template>

<script>

    export default {

        name: "Pricing"

    }

</script>
