<template>
    <div class="faq">
        <h1 class="heading">Frequently Asked Questions</h1>

        <div
            v-for="(faqItem, index) in faq"
            :key="'faq_'+index"
            :class="[
                'faq__item',
                 {'faq__item--current': currentHash === '#'+faqItem.id}
            ]"
            :id="faqItem.id"
        >
            <div class="faq__question">
                <i class="fas fa-question-circle faq__icon"></i>
                {{faqItem.question}}

                <a
                    :href="'#'+faqItem.id"
                    @click="changeHash"
                    class="faq__question-link"
                >
                    <i class="icon-fa icon-fa--inline fas fa-hashtag"></i>link
                </a>
            </div>
            <div class="faq__answer">
                <i class="far fa-lightbulb faq__icon faq__icon--answer"></i>
                <div v-html="faqItem.answer"></div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "FaqPage",
        data () {
            return {
                currentHash: location.hash,
                faq: [
                    {
                        id: 'website_info',
                        question: 'We got a website where you can submit the games you are going to play now?',
                        answer:
                            `Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.`,
                    },
                    {
                        id: 'all_wins_beaten',
                        question: 'Have already completed all your wins?',
                        answer: `
                            <div>You can place HTML in answers</div>
                            <a href="https://steamgifts.com" target="_blank">and it will even work</a>
                            `
                    },
                ],
            };
        },
        methods: {
            changeHash() {
                setTimeout(() => {
                    this.currentHash = location.hash;
                },25);
            }
        }
    }
</script>

<style lang="less">
    @import '../assets/mixins';

    .faq{

        &__item{
            margin-bottom: 40px;
            border: 1px solid transparent;
            transition: box-shadow 0.3s, border-color 0.3s;

            &--current{
                border: 1px solid @color-light-blue;
                box-shadow: 0 0 14px @color-light-blue;
            }
        }

        &__question, &__answer{
            padding: 14px 16px 14px 44px;
            position: relative;
        }

        &__question{
            font-size: 16px;
            font-weight: bold;
            color: @color-pink;
            border-left: 4px solid @color-pink;
            background: fade(@color-light-pink, 10%);
            border-bottom: 1px solid @color-pink;
        }
        
        &__question-link{
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
        }

        &__answer{
            border-left: 4px solid @color-blue;
            background-color: fade(@color-blue, 10%);
        }

        &__icon{
            position: absolute;
            left: 10px;
            transform: translateY(-50%);
            font-size: 20px;
            top: 50%;

            &--answer{
                top: 24px;
                color: @color-blue;
            }
        }

    }
</style>