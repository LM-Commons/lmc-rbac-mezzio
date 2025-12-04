import clsx from 'clsx';
import Heading from '@theme/Heading';
import styles from './styles.module.css';
import Link from "@docusaurus/Link";
import packageConfig from "../../../config/index.js";

export default function HomepageFeatures() {
  return (
    <section className={styles.features}>
      <div className="container">
        <div className="row">
            <div className={clsx("col col--8")}>
                <Heading as="h1">Introduction</Heading>
                <p>LM-Commons RBAC Mezzio extends on <a href="https://lm-commons.github.io/LmcRbac/">LM-Commons RBAC</a> to
                    provide guards and strategies to implement Role-Based Access Control in Mezzio applications.
                </p>
                <Heading as="h2">Support</Heading>
                <ul>
                    <li>File issues at <Link
                        href={'https://github.com/LM-Commons/'+packageConfig.projectName+'/issues'}>
                        github.com/LM-Commons/{packageConfig.projectName}/issues</Link>.
                    </li>
                    <li>Ask questions in the <Link
                        href="https://discord.gg/MSQZQJcS4S">LM-Commons
                        Discord</Link> chat.
                    </li>
                </ul>
            </div>
        </div>
      </div>
    </section>
  );
}
