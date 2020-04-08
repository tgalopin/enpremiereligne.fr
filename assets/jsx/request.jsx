import { h, render } from 'preact';
import {useState} from 'preact/hooks';
import {trans} from './translator';

const NeedGroceriesButton = ({ needGroceries, onChange }) => {
    return (
        <button className={'btn btn-dark process-needs-button text-left '+(needGroceries ? 'active' : '')}
                type="button"
                onClick={() => onChange(!needGroceries)}>
            <div className="d-flex">
                <div className="process-needs-checkbox">
                    {needGroceries
                        ? (
                            <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 448 512">
                                <path fill="currentColor"
                                      d="M400 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V80c0-26.51-21.49-48-48-48zm0 32c8.823 0 16 7.178 16 16v352c0 8.822-7.177 16-16 16H48c-8.822 0-16-7.178-16-16V80c0-8.822 7.178-16 16-16h352m-34.301 98.293l-8.451-8.52c-4.667-4.705-12.265-4.736-16.97-.068l-163.441 162.13-68.976-69.533c-4.667-4.705-12.265-4.736-16.97-.068l-8.52 8.451c-4.705 4.667-4.736 12.265-.068 16.97l85.878 86.572c4.667 4.705 12.265 4.736 16.97.068l180.48-179.032c4.704-4.667 4.735-12.265.068-16.97z" />
                            </svg>
                        )
                        : (
                            <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 448 512">
                                <path fill="currentColor"
                                      d="M400 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48zm16 400c0 8.8-7.2 16-16 16H48c-8.8 0-16-7.2-16-16V80c0-8.8 7.2-16 16-16h352c8.8 0 16 7.2 16 16v352z" />
                            </svg>
                        )
                    }
                </div>

                <div>
                    <h4>
                        {trans('request.groceries.label')}
                    </h4>

                    <div>
                        {trans('request.groceries.help')}
                    </div>
                </div>
            </div>
        </button>
    );
};

const NeedBabysitButton = ({ needBabysit, onChange }) => {
    return (
        <button className={'btn btn-dark process-needs-button text-left '+(needBabysit ? 'active' : '')}
                type="button"
                onClick={() => onChange(!needBabysit)}>
            <div className="d-flex">
                <div className="process-needs-checkbox">
                    {needBabysit
                        ? (
                            <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 448 512">
                                <path fill="currentColor"
                                      d="M400 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V80c0-26.51-21.49-48-48-48zm0 32c8.823 0 16 7.178 16 16v352c0 8.822-7.177 16-16 16H48c-8.822 0-16-7.178-16-16V80c0-8.822 7.178-16 16-16h352m-34.301 98.293l-8.451-8.52c-4.667-4.705-12.265-4.736-16.97-.068l-163.441 162.13-68.976-69.533c-4.667-4.705-12.265-4.736-16.97-.068l-8.52 8.451c-4.705 4.667-4.736 12.265-.068 16.97l85.878 86.572c4.667 4.705 12.265 4.736 16.97.068l180.48-179.032c4.704-4.667 4.735-12.265.068-16.97z" />
                            </svg>
                        )
                        : (
                            <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 448 512">
                                <path fill="currentColor"
                                      d="M400 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48zm16 400c0 8.8-7.2 16-16 16H48c-8.8 0-16-7.2-16-16V80c0-8.8 7.2-16 16-16h352c8.8 0 16 7.2 16 16v352z" />
                            </svg>
                        )
                    }
                </div>

                <div>
                    <h4>
                        {trans('request.babysit.label')}
                    </h4>

                    <div>
                        {trans('request.babysit.help')}
                    </div>
                </div>
            </div>
        </button>
    );
};

const HiddenInputs = ({ needGroceries, needBabysit, children }) => {
    let inputs = [];
    let key = 0;

    if (needGroceries) {
        inputs.push(
            <div key={'input-'+key}>
                <input type="hidden" name={'composite_help_request[details]['+key+'][helpType]'} value="groceries" />
                <input type="hidden" name={'composite_help_request[details]['+key+'][childAgeRange]'} value="" />
            </div>
        );

        key++;
    }

    if (needBabysit) {
        for (let i in children) {
            inputs.push(
                <div key={'input-'+key}>
                    <input type="hidden" name={'composite_help_request[details]['+key+'][helpType]'} value="babysit" />
                    <input type="hidden" name={'composite_help_request[details]['+key+'][childAgeRange]'} value={children[i].age} />
                </div>
            );

            key++;
        }
    }

    return <div>{inputs}</div>;
};

const NeedsChooser = () => {
    const [needGroceries, setNeedGroceries] = useState(false);
    const [needBabysit, setNeedBabysit] = useState(false);
    const [children, setChildren] = useState([{ age: null }]);

    const updateAge = (key, age) => {
        setChildren(old => {
            let newArray = [];
            for (let i in old) {
                if (i + '' !== key + '') {
                    newArray.push(old[i]);
                } else {
                    newArray.push({ age: age });
                }
            }

            return newArray;
        });
    };

    const removeChild = (key) => {
        setChildren(old => {
            let newArray = [];
            for (let i in old) {
                if (i + '' !== key + '') {
                    newArray.push(old[i]);
                }
            }

            return newArray;
        });
    };

    return (
        <div className="row">
            <div className="col-12 col-lg-6 mb-3">
                <NeedGroceriesButton needGroceries={needGroceries} onChange={setNeedGroceries} />
            </div>
            <div className="col-12 col-lg-6">
                <NeedBabysitButton needBabysit={needBabysit} onChange={setNeedBabysit} />

                <div className={'p-3 '+(!needBabysit ? 'process-needs-disabled' : '')}>
                    <div className="form-group">
                        <div className="custom-control custom-checkbox">
                            <input type="checkbox"
                                   id="helper_preferParents"
                                   name="helper[preferParents]"
                                   className="custom-control-input" />

                            <label className="checkbox-custom custom-control-label" htmlFor="helper_preferParents">
                                {trans('request.babysit.already_parent.label')}
                            </label>
                        </div>

                        <div className="mt-2">
                            <small id="helper_acceptVulnerable_help" className="form-text text-muted">
                                {trans('request.babysit.already_parent.help')}
                            </small>
                        </div>
                    </div>

                    <div className="mt-4 mb-2">
                        <strong>
                            {trans('request.babysit.ages.label')}
                        </strong>
                    </div>

                    <div className="mb-4">
                        {children.map((child, key) => {
                            return (
                                <div className="mb-2" key={key+'-'+child.age}>
                                    <small className="text-muted text-uppercase">
                                        {trans('request.babysit.ages.child') + (key + 1)}
                                    </small>

                                    <div className="row no-gutters">
                                        <div className="col-10">
                                            <select className="form-control"
                                                    required={needBabysit && parseInt(key) === 0}
                                                    value={child.age}
                                                    onChange={e => updateAge(key, e.target.value)}>
                                                <option value={null} />
                                                <option value="0-1">{trans('request.babysit.ages.0-1')}</option>
                                                <option value="1-2">{trans('request.babysit.ages.1-2')}</option>
                                                <option value="3-5">{trans('request.babysit.ages.3-5')}</option>
                                                <option value="6-9">{trans('request.babysit.ages.6-9')}</option>
                                                <option value="10-12">{trans('request.babysit.ages.10-12')}</option>
                                                <option value="13-">{trans('request.babysit.ages.13')}</option>
                                            </select>
                                        </div>
                                        <div className="col-2 text-center">
                                            <button type="button" className={'btn btn-link '+(key === 0 ? 'd-none' : '')}
                                                    onClick={() => removeChild(key)}>
                                                <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                                    <path fill="currentColor"
                                                          d="M193.94 256L296.5 153.44l21.15-21.15c3.12-3.12 3.12-8.19 0-11.31l-22.63-22.63c-3.12-3.12-8.19-3.12-11.31 0L160 222.06 36.29 98.34c-3.12-3.12-8.19-3.12-11.31 0L2.34 120.97c-3.12 3.12-3.12 8.19 0 11.31L126.06 256 2.34 379.71c-3.12 3.12-3.12 8.19 0 11.31l22.63 22.63c3.12 3.12 8.19 3.12 11.31 0L160 289.94 262.56 392.5l21.15 21.15c3.12 3.12 8.19 3.12 11.31 0l22.63-22.63c3.12-3.12 3.12-8.19 0-11.31L193.94 256z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}

                        <div className={'text-right mt-3 '+(children.length >= 4 ? 'd-none' : '')}>
                            <button type="button" className="btn btn-sm btn-link"
                                    onClick={() => setChildren(old => [...old, { age: null }])}>
                                {trans('request.babysit.ages.add')}
                            </button>
                        </div>

                        <div className="text-muted">
                            <small>
                                {trans('request.babysit.ages.help')}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div className="d-none">
                <HiddenInputs needGroceries={needGroceries} needBabysit={needBabysit} children={children} />
            </div>
        </div>
    );
};

const wrapper = document.getElementById('request-needs');
wrapper.innerHTML = '';

render(<NeedsChooser />, wrapper);
