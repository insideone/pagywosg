<template>
    <div class="text">
        <h1 class="heading">How to Format Your Text</h1>

        <p>This site uses Markdown for text formatting. The most important formatting tips are located on this page, but you can get more information on the official Markdown <a href="https://daringfireball.net/projects/markdown/syntax" target="_blank">website</a>.</p>

        <h2 class="heading heading--h2">Headings</h2>
        <table>
            <tr>
                <th>Markdown</th>
                <th>Output</th>
            </tr>
            <tr>
                <td>
                    <pre># Heading 1
## Heading 2
### Heading 3
#### Heading 4
##### Heading 5
####### Heading 6</pre>
                </td>
                <td>
                    <h1>Heading 1</h1>
                    <h2>Heading 2</h2>
                    <h3>Heading 3</h3>
                    <h4>Heading 4</h4>
                    <h5>Heading 5</h5>
                    <h6>Heading 6</h6>
                </td>
            </tr>
            <tr>
                <td>
                    <p>Alternative syntax</p>
                    <pre>Heading 1
====
Heading 2
----</pre>
                </td>
                <td>
                    <h1>Heading 1</h1>
                    <h2>Heading 2</h2>
                </td>
            </tr>
        </table>


        <h2 class="heading heading--h2">Text emphasis</h2>
        <table>
            <tr>
                <th>Markdown</th>
                <th>Output</th>
            </tr>
            <tr>
                <td>
                        <pre>**bold text**
__bold text__
*italic text*
_italic text_
***bold and italic text***
___bold and italic text___
~Strikethrough text~</pre>
                </td>
                <td>
                    <p>
                        <strong>bold text</strong><br />
                        <strong>bold text</strong><br />
                        <em>italic text</em><br />
                        <em>italic text</em><br />
                        <strong><em>bold and italic text</em></strong><br />
                        <strong><em>bold and italic text</em></strong><br />
                        <del>Strikethrough text</del>
                    </p>
                </td>
            </tr>
        </table>

        <h2 class="heading heading--h2">Blockquotes</h2>
        <table>
            <tr>
                <th>Markdown</th>
                <th>Output</th>
            </tr>
            <tr>
                <td><pre>> An example of a quote</pre></td>
                <td>
                    <blockquote>
                        <p>An example of a quote</p>
                    </blockquote>
                </td>
            </tr>
        </table>

        <h2 class="heading heading--h2">Lists</h2>
        <table>
            <tr>
                <th>Markdown</th>
                <th>Output</th>
            </tr>
            <tr>
                <td>
                    <p>Ordered list:</p>
                    <pre>1. First
2. Second
3. Third</pre>
                    <p>Unordered list:</p>
                    <pre>* First
* Second
* Third</pre>
                    <p>Lists can be nested:</p>
                    <pre>1. First
2. Second
* next level
* one more item
3. Third</pre>
                </td>
                <td>
                    <p>Ordered list:</p>
                    <ol>
                        <li>First</li>
                        <li>Second</li>
                        <li>Third</li>
                    </ol>
                    <p>Unordered list:</p>
                    <ul>
                        <li>First</li>
                        <li>Second</li>
                        <li>Third</li>
                    </ul>
                    <p>Lists can be nested:</p>
                    <ol>
                        <li>First</li>
                        <li>Second<ul>
                            <li>next level</li>
                            <li>one more item</li>
                        </ul>
                        </li>
                        <li>Third</li>
                    </ol>
                </td>
            </tr>
        </table>

        <h2 class="heading heading--h2">Code</h2>
        <table>
            <tr>
                <th>Markdown</th>
                <th>Output</th>
            </tr>
            <tr>
                <td><pre>Code can be `inline`

Code can be multiline:
```
&lt;script&gt;
    alert( 'Hello, world!' );
&lt;/script&gt;
```</pre></td>
                <td>
                    <p>Code can be <code>inline</code></p>
                    <p>Code can be multiline:</p>
                    <pre>&lt;script&gt;
    alert( 'Hello, world!' );
&lt;/script&gt;</pre>
                </td>
            </tr>
        </table>

        <h2 class="heading heading--h2">Links</h2>
        <table>
            <tr>
                <th>Markdown</th>
                <th>Output</th>
            </tr>
            <tr>
                <td><pre>[Link example](https://google.com)</pre></td>
                <td>
                    <p><a href="https://google.com">Link example</a></p>
                </td>
            </tr>
        </table>

        <h2 class="heading heading--h2">Images</h2>
        <table>
            <tr>
                <th>Markdown</th>
                <th>Output</th>
            </tr>
            <tr>
                <td><pre>![image](https://steamcommunity-a.akamaihd.net/public/images/badges/25_steamawardnominations/level04_80.png)</pre></td>
                <td>
                    <img src="https://steamcommunity-a.akamaihd.net/public/images/badges/25_steamawardnominations/level04_80.png" alt="image">
                </td>
            </tr>
        </table>

        <h2 class="heading heading--h2">Horizontal Rules</h2>
        <table>
            <tr>
                <th>Markdown</th>
                <th>Output</th>
            </tr>
            <tr>
                <td><pre>Text before

---

Text after</pre></td>
                <td>
                    <p>Text before</p>
                    <hr>
                    <p>Text after</p>
                </td>
            </tr>
        </table>

        <h2 class="heading heading--h2">Tables</h2>
        <table>
            <tr>
                <th>Markdown</th>
                <th>Output</th>
            </tr>
            <tr>
                <td><pre>Heading 1 | Heading 2
- | -
Cell 1 | Cell 2
Cell 3 | Cell 4</pre></td>
                <td>
                    <table>
                        <tr>
                            <th>Heading 1</th>
                            <th>Heading 2</th>
                        </tr>
                        <tr>
                            <td>Cell 1</td>
                            <td>Cell 2</td>
                        </tr>
                        <tr>
                            <td>Cell 3</td>
                            <td>Cell 4</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <pre>| A table | with | alignment
:- |    :-:   | -:
Left | Center | Right
Example | Text | End</pre>
                </td>
                <td>
                    <table>
                        <thead>
                        <tr>
                            <th align="left">A table</th>
                            <th align="center">with</th>
                            <th align="right">alignment</th>
                        </tr>
                        </thead>
                        <tbody><tr>
                            <td align="left">Left</td>
                            <td align="center">Center</td>
                            <td align="right">Right</td>
                        </tr>
                        <tr>
                            <td align="left">Example</td>
                            <td align="center">Text</td>
                            <td align="right">End</td>
                        </tr>
                        </tbody></table>
                </td>
            </tr>
        </table>

        <h2 class="heading heading--h2">Escaping Characters</h2>
        <p>To display a literal character that would otherwise be used to format text in a Markdown document, add a backslash (\) in front of the character.</p>

    </div>
</template>

<script>
    export default {
        name: "HelpFormatting"
    }
</script>

<style lang="less">
    @import "../assets/mixins";

</style>