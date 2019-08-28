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
                <div class="faq__question-text">{{faqItem.question}}</div>
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
                <div v-html="faqItem.answer" @click="changeHash"></div>
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
                        id: 'participate',
                        question: 'How do I participate?',
                        answer:
                            `Click on the "Sign in through Steam" button on the top right of the page.<br>
                            <img src="/img/steam_login.png" alt="Steam login"><br>
                            Once you have successfully logged-in click on the event button top left and then once again click on the event that is currently going on. Example:<br>
                            <img src="/img/event.png" alt="Event selection" width="840"><br>
                            Then click on the "Add new game button at either the top or bottom of the game submissions."<br>
                            <img src="/img/add_game.png" alt="adding game"><br>
                            Fill in the required data and submit it.<br>
                            <img src="/img/adding_game.png" alt="Adding a game" width="840"><br>
                            If you already have some playtime/achievements in the game or your messed up your submission click the edit button on the top right of your game submission, fill in the missing data and submit the game again.<br>
                            <img src="/img/edit_game.png" alt="Editing a game" width="840"><br>
                            `,
                    },
                    {
                        id: 'updating_playtime',
                        question: 'Do I need to manually update my playtime stats?',
                        answer:
                            `Nope, it is all done automatically :) All you need to do is set the status of the game to "Completed" or "Beaten" once you actually complete/beat the game.`,
                    },
                    {
                        id: 'playtime_not_updating',
                        question: 'But it has been over 24 hours and my stats haven\'t been updated!',
                        answer:
                            `Contact MouseWithBeer, you can see how <a href="#no_answer">here</a>.`,
                    },
                    {
                        id: 'rewards',
                        question: 'I have completed a game, where can I see my rewards?!?!',
                        answer:
                            `The rewards will appear once I check and approve the playtime to make sure the game was actually played. It normally shouldn't take more than 24 hours. Once I approve it you will be able to see the giveaway links one the event page under the description and above the game submissions where it says "Congrats on beating one (or more) of your SG wins! Rewards will be available shortly!" on the image below:<br>
                            <img src="/img/reward.png" alt="location of the reward" height="300"><br>`,
                    },
                    {
                        id: 'without_login',
                        question: 'What can I do if I want to participate but I don\'t want to login in the website?',
                        answer: `
                                Leave a comment in the SteamGifts event thread for the right month saying which games you will be playing and that you don't want to login into the site. I will add the games to the site myself.
                            `
                    },
                    {
                        id: 'all_wins_beaten',
                        question: 'Have already completed all your qualifying wins?',
                        answer: `
                                Feel free to participate with any other games that fit the theme from your Steam library.
                            `
                    },
                    {
						id: 'dlc',
						question: 'Can I submit a DLC I have won?',
						answer: `
								 Yes. If either the DLC or the base game for that DLC is part of the theme you can submit that. Please submit the base game (you can not submit DLC at all on the site) and leave a note on the submission that you have won the DLC, so I will know when checking if the game was indeed a SG win. 
							`
					},
                    {
						id: 'unfinished_game',
						question: 'Can I submit a game I have played before the start of the month?',
						answer: `
								As long as the game is not 95 or so percent finished, sure go ahead but please fill in the starting achievements/playtime. You can see how to do that <a href="#participate">here</a>.
								`
					},
					{
						id: 'late_submission',
						question: 'I finished a game this month that is perfect for the theme without submitting it beforehand. Can I submit it anyways?',
						answer: `
								Sure! It doesn't matter if you submit the game after it was already completed or before as long as it was played that month.
								`
					},
                    {
                        id: 'bug_suggestion',
                        question: 'I think I found a bug/I have a suggestion for the website! What should I do?',
                        answer: `
                                You can either open a new issue on <a href="https://github.com/insideone/pagywosg/issues" target="_blank">Github</a> or report them directly to me on Steam/SteamGifts.
                            `
                    },
                    {
                        id: 'why',
                        question: 'Why do you need a website for this event?',
                        answer: `
                                Because it used to take Mouse 2-6 hours every week to go through every submission and she got tried of it.
                            `
                    },
                    {
                        id: 'steam_group',
                        question: 'Can I join the Steam group?',
                        answer: `
                                Unless I explicitly tell you to request to join: no. The Steam group is reserved for people who have already successfully participated in the event at least once in the last 6 months. If you have done that and you were not told to join the group leave a comment about it in the current event thread on Steamgifts and I will take a look.
                            `
                    },
                    {
                        id: 'no_answer',
                        question: 'None of these answers are the answer to my question. What should I do?',
                        answer: `
                                You can either comment with your question in the ongoing event thread on SteamGifts or you can try to contact <a href="https://steamcommunity.com/id/MouseWithBeer" target="_blank">MouseWithBeer</a> directly on Steam (please leave a comment in the comment section when adding as friend)
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
            display: flex;
            align-items: center;
        }

        &__question-text{
            flex-grow: 1;
        }
        
        &__question-link{
            white-space: nowrap;
            margin-left: 10px;
            flex-shrink: 0;
        }

        &__answer{
            border-left: 4px solid @color-blue;
            background-color: fade(@color-blue, 10%);

            & img{
                margin: 5px 0;
                max-width: 100%;
            }
        }

        &__icon{
            position: absolute;
            left: 10px;
            transform: translateY(-50%);
            font-size: 20px;
            top: 50%;
            width: 25px;
            text-align: center;

            &--answer{
                top: 24px;
                color: @color-blue;
            }
        }

    }
</style>