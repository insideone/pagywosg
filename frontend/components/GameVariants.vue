<template>
    <div class="variants">
        <div
            v-for="(game, key) in games"
            :key="'game_variant_'+key"
            class="variants__item"
            @click="gameSelected(game)"
        >
            <div class="variants__item-pic-block">
                <div
                    :style="'background-image: url(https://steamcdn-a.akamaihd.net/steam/apps/'+game.id+'/capsule_184x69.jpg);'"
                    class="variants__item-img"
                ></div>
            </div>
            <div class="variants__item-body">
                <div class="variants__item-name">{{game.name}}</div>
                <a
                    :href="'https://store.steampowered.com/app/'+game.id+'/'"
                    target="_blank"
                >https://store.steampowered.com/app/{{game.id}}/</a>
            </div>
        </div>
        <div
            v-if="(totalPages > 1) && games.length"
            class="pagin"
        >
            <a
                v-if="curPage > 1"
                class="pagin__link"
                @click.prevent="callSearchGames(1)"
            >&lt;&lt;</a>

            <component
                v-for="i in range(minPageToShow, maxPageToShow)"
                :is="(i === curPage) ? 'span' : 'a'"
                :key="'page_'+i"
                :class="(i === curPage) ? 'pagin__cur' : 'pagin__link'"
                @click.prevent="(i !== curPage) ? callSearchGames(i) : ''"
            >{{i}}</component>

            <a
                v-if="curPage < totalPages"
                class="pagin__link"
                @click.prevent="callSearchGames(totalPages)"
            >&gt;&gt;</a>

        </div>
    </div>
</template>

<script>
    import {mapActions} from 'vuex';
    import debounce from '../services/debounce';

    export default {
        name: "GameVariants",
        props: {
            searchString: {
                type: String,
                default: ''
            },
            isSearching: {
                type: Boolean,
                default: false
            }
        },
        data: function () {
            return {
                games: [],
                curPage: 1,
                totalPages: 1
            };
        },
        computed: {
            minPageToShow: function() {
                let minPage = this.curPage - 2;

                if (minPage < 1)
                    minPage = 1;

                return minPage;
            },
            maxPageToShow: function() {
                let maxPage = this.curPage + 2;

                if (maxPage > this.totalPages)
                    maxPage = this.totalPages;

                return maxPage;
            }
        },
        watch: {
            searchString: function () {
                this.handleSearchStringChange();
            }
        },
        methods: {
            ...mapActions({
                searchGames: 'searchGames'
            }),

            handleSearchStringChange() {

                if (!this.isSearching)
                {
                    this.games = [];
                    return;
                }

                if (this.searchString === '')
                {
                    this.games = [];
                    return;
                }


                debounce.debounce(() => {
                    this.callSearchGames();
                }, 500)();
            },

            gameSelected (game) {
                this.$emit('game-selected', game);
            },

            callSearchGames (page = 1) {
                this.searchGames({query: this.searchString, page: page}).then(({games, maxPageNumber}) => {
                    this.games = games;
                    this.totalPages = maxPageNumber;
                    this.curPage = page;
                });
            },

            range(start, end){
                return Array(end - start + 1).fill(0).map((val, i) => i + start);
            }
        },
    }
</script>

<style lang="less">
    @import "../assets/blocks/variants";
    @import "../assets/blocks/pagin";
</style>