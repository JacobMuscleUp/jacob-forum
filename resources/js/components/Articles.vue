<template>
    <div class="container">
        <form v-on:submit.prevent="updateArticle">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Title" v-model="article.title">
            </div>
            <div class="form-group">
                <textarea class="form-control" placeholder="Body" v-model="article.body"></textarea>
            </div>
            <button type="submit" class="btn btn-light btn-block">Submit</button>
        </form>
        <nav aria-label="page navigation">
            <ul class="pagination">
                <li class="page-item"
                    v-bind:class="{'disabled': !pagination.prevPageUrl}">
                    <a class="page-link" 
                        href="#"
                        v-on:click="fetchArticles(pagination.prevPageUrl)">
                        Prev
                    </a>
                </li>
                <li class="page-item disabled">
                    <a class="page-link text-dark" href="#">
                        Page {{ pagination.currentPage }} of {{ pagination.lastPage }}
                    </a>
                </li>
                <li class="page-item"
                    v-bind:class="{'disabled': !pagination.nextPageUrl}">
                    <a class="page-link" 
                        href="#"
                        v-on:click="fetchArticles(pagination.nextPageUrl)">
                        Next
                    </a>
                </li>
            </ul>
        </nav>
        <form v-on:submit.prevent="gotoTargetPage">
            <input type="text" id="target-page" placeholder="Page" v-model="targetPage">
            <button type="submit" class="btn btn-light btn-block">Go</button>
        </form>
        <div 
            class="card card-body mb-2"
            v-for="article in articles"
            v-bind:key="article.id">
            <h3>{{ article.title }}</h3>
            <p>{{ article.body }}</p>
            <p>Authored by <b>{{ article.author }}</b></p>
            <hr>
            <template v-if="strcmp(user, article.author) === 0 || isAdmin">
                <button 
                    class="btn btn-warning mb-2"
                    v-on:click="editArticle(article)">
                    Edit
                </button>
                <button 
                    class="btn btn-danger"
                    v-on:click="deleteArticle(article)">
                    Delete
                </button>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            user: {
                type: String
            },
            fetchAll: {
                type: Boolean
            }
        },
        data() {
            return {
                articles: [],
                article: {
                    id: '',
                    title: '',
                    body: '',
                    author: this.user
                },
                pagination: '',
                editMode: false,
                targetPage: ''
            };
        },
        created() {
            this.fetchArticles(`/index.php/api/articles${this.fetchAll ? '' : `/${this.user}`}`);
        },
        computed: {
            isAdmin() {
                return this.strcmp(this.user, 'admin') === 0; 
            }
        },
        methods: {
            fetchArticles(pageUrl, callback) {
                let instance = this;
                fetch(pageUrl)
                    .then(res => res.json())
                    .then(res => {
                        //console.log(res.data);
                        this.articles = res.data;
                        instance.makePagination(res.meta, res.links);
                        if (callback)
                            callback();
                    })
                    .catch(err => console.log(err));
            },
            makePagination(meta, links) {
                let pagination = {
                    currentPage: meta.current_page,
                    lastPage: meta.last_page,
                    prevPageUrl: links.prev,
                    nextPageUrl: links.next
                };
                this.pagination = pagination;
            },
            deleteArticle(article) {
                if (confirm('Please confirm the deletion')) {
                    fetch(`/index.php/api/article/${article.id}`, {
                        method: 'delete'
                    })
                        .then(res => res.json)
                        .then(res => {
                            this.fetchArticles(`/index.php/api/articles${this.fetchAll ? '' : `/${this.user}`}`, function() {
                                alert('the article has been removed');
                            });
                        })
                        .catch(err => console.log(err));
                }
            },
            updateArticle() {
                const instance = this;
                const func = function(method, msg) {
                    fetch('/index.php/api/article', {
                        method: method,
                        body: JSON.stringify(instance.article),
                        headers: {
                            'content-type': 'application/json'
                        } 
                    })
                        .then(res => res.json())
                        .then(res => {
                            instance.article.title = '';
                            instance.article.body = '';
                            instance.fetchArticles(`/index.php/api/articles${instance.fetchAll ? '' : `/${instance.user}`}`, function() {
                                alert(msg);
                            });
                        })
                        .catch(err => console.log(err));
                };
                if (this.editMode)
                    func('put', 'The article has been edited');
                else
                    func('post', "An article has been created");
            },
            editArticle(article) {
                this.editMode = true;
                this.article.id = article.id;
                this.article.title = article.title;
                this.article.body = article.body;
            },
            gotoTargetPage() {
                const targetPage = parseInt(this.targetPage);
                if (isNaN(targetPage))
                    alert('The page needs to be an integer');
                else if (Math.min(Math.max(1, targetPage), this.pagination.lastPage) !== targetPage)
                    alert('The page is out of range');
                else
                    this.fetchArticles(`/index.php/api/articles${this.fetchAll ? '' : `/${this.user}`}?page=${this.targetPage}`);

                document.getElementById('target-page').value = this.targetPage = '';
            },
            strcmp(a, b) {
                a = a.toString(), b = b.toString();
                for (var i = 0, n = Math.max(a.length, b.length); 
                    i < n && a.charAt(i) === b.charAt(i); 
                    ++i) {}
                if (i === n) 
                    return 0;
                return a.charAt(i) > b.charAt(i) ? -1 : 1;
            }
        }
    }
</script>